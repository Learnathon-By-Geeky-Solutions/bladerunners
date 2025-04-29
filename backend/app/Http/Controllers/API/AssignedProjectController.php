<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AssignedProject;
use App\Models\Bidding;
use App\Models\PaymentHistory;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignedProjectController extends Controller
{
    // List all assignments (visible to clients, freelancers, and admins)
    public function index()
    {
        $assignments = AssignedProject::with(['project', 'client', 'freelancer', 'review'])->get();
        return response()->json($assignments, 200);
    }

    // Show a single assignment by ID
    public function show($projectId)
    {
        $assignment = AssignedProject::where('project_id', $projectId)
                                    ->with(['project', 'client', 'freelancer', 'review'])
                                    ->firstOrFail();

        return response()->json($assignment, 200);
    }

    // Create a new project assignment (only the client who created the project can assign it)
    public function store(Request $request)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'project_id'     => 'required|exists:projects,id',   // Project ID
            'freelancer_id'  => 'required|exists:freelancers,freelancer_id',  // Freelancer ID
        ]);

        // Retrieve the project and ensure that the authenticated client is the owner
        $project = Project::findOrFail($validatedData['project_id']);
        if (Auth::user()->id !== $project->client_id) {
            return response()->json(['error' => 'Unauthorized: You do not own this project'], 403);
        }

        // Retrieve the bidding record for the selected freelancer
        $bidding = Bidding::where('project_id', $validatedData['project_id'])
                        ->where('freelancer_id', $validatedData['freelancer_id'])
                        ->first();

        if (!$bidding) {
            return response()->json(['error' => 'No bid found from the selected freelancer for this project'], 404);
        }

        // Get the bidding amount
        $paymentAmount = $bidding->bidding_amount;

        // Auto-generate the deadline (7 days from today)
        $deadline = now()->addDays(7);

        // Update the project's status to "Assigned" when it is assigned
        $project->status = 'Assigned';
        $project->assigned_freelancer_id = $validatedData['freelancer_id'];
        $project->save();

        // Create the assignment record with necessary details
        $assignment = AssignedProject::create([
            'project_id'       => $validatedData['project_id'],
            'freelancer_id'    => $validatedData['freelancer_id'],
            'payment_amount'   => $paymentAmount,  // Use bidding amount
            'deadline'         => $deadline,
            'status'           => 'Assigned',  // Automatically set to 'Assigned'
            'payment_status'   => 'Pending',   // Automatically set to 'Pending'
            'client_id'        => Auth::user()->id,  // Authenticated client
            'assigned_date'    => now(),
        ]);

        return response()->json($assignment, 201);  // Return the created assignment
    }



    // Update an assignment (for example, to change status, deadline, or payment info)
    public function update(Request $request, $projectId)
    {
        // 1. Fetch assignment or 404
        $assignment = AssignedProject::where('project_id', $projectId)->firstOrFail();

        // 2. Validate incoming status & related fields
        $validated = $request->validate([
            'status'         => 'sometimes|in:Assigned,In Progress,Completed,Canceled,Submitted',
            'completion_date'=> 'sometimes|nullable|date',
            'review_id'      => 'sometimes|nullable|exists:reviews,id',
        ]);

        // 3. Prepare the project and the (firstOrNew) payment record
        $project = Project::findOrFail($assignment->project_id);

        $payment = PaymentHistory::firstOrNew([
            'project_id'  => $assignment->project_id,
            'sender_id'   => $assignment->client_id,
            'receiver_id' => $assignment->freelancer_id,
        ], [
            'amount'       => $assignment->payment_amount,
            'payment_type' => 'Fixed-Price',
            'status'       => 'Pending',
        ]);

        // 4. Branch by new status
        switch ($validated['status'] ?? null) {
            case 'Submitted':
                $assignment->status = 'Submitted';
                $project->status    = 'Submitted';
                break;

            case 'Completed':
                // a) Assignment & payment_status
                $assignment->status         = 'Completed';
                $assignment->payment_status = 'Released';

                // b) If payment is brand-new, deduct from client
                if (! $payment->exists) {
                    $client = User::findOrFail($assignment->client_id);
                    if ($client->balance < $assignment->payment_amount) {
                        return response()->json(['error' => 'Client has insufficient balance.'], 400);
                    }
                    $client->balance -= $assignment->payment_amount;
                    $client->save();
                }

                // c) Mark payment completed
                $payment->status = 'Completed';

                // d) Split to admin & freelancer
                $admin = User::whereIn('id', function ($q) {
                    $q->select('admin_id')->from('admins')->where('is_super_admin', 1);
                })->first();

                $freelancerUser = User::findOrFail($payment->receiver_id);

                if ($payment->payment_type === 'Escrow') {
                    $adminShare      = $payment->amount * 0.05;
                    $freelancerShare = $payment->amount * 0.95;
                } else {
                    $adminShare      = $payment->amount * 0.10;
                    $freelancerShare = $payment->amount * 0.90;
                }

                $admin->balance         += $adminShare;
                $freelancerUser->balance += $freelancerShare;
                $admin->save();
                $freelancerUser->save();

                $project->status = 'Closed';
                break;

            case 'Canceled':
                $assignment->status = 'Canceled';
                // If no record existed, firstOrNew created it—so just mark failed.
                $payment->status = 'Failed';
                $project->status = 'Closed';
                break;

            default:
                // Any other fields to update?
                $assignment->update($validated);
                // We’ll still save project/payment below.
        }

        // 5. Persist everything
        $assignment->save();
        $payment->save();
        $project->save();

        // 6. Single successful return
        return response()->json($assignment, 200);
    }

    // Delete an assignment
    public function destroy($id)
    {
        $assignment = AssignedProject::findOrFail($id);
        $assignment->delete();

        return response()->json(['message' => 'Assignment deleted'], 200);
    }


    /**
     * GET /freelancer/assignments
     *
     * Returns all assignments assigned to the authenticated freelancer.
     */
    public function myAssignments()
    {
        $freelancerId = Auth::id();

        $assignments = AssignedProject::where('freelancer_id', $freelancerId)
            ->with(['project', 'client', 'freelancer', 'review'])
            ->get();

        return response()->json($assignments, 200);
    }

}

