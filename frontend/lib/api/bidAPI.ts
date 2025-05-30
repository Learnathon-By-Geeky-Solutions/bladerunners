import apiRequest from "@/lib/api/apiRequest";
import { toast } from "sonner";

export const createBiddingAPI = async (
  projectId: number,
  data: {
    cover_letter: string;
    bidding_amount: number;
  }
) => {
  const response = await apiRequest({
    method: "POST",
    url: `/projects/${projectId}/biddings`,
    data: {
      cover_letter: data.cover_letter,
      bidding_amount: data.bidding_amount,
    },
  });

  if (response.status === 201) {
    toast.success("Bid placed successfully");
    return response.data;
  } else if (response.status === 422) {
    toast.error(response.data.message || "Validation error");
    return null;
  } else if (response.status === 401) {
    toast.error("Unauthorized. Please sign in as a freelancer.");
    return null;
  } else if (response.status === 404) {
    toast.error("Project not found");
    return null;
  } else {
    toast.error(response.data.message || "Failed to place bid");
    return null;
  }
};


export const getBiddingsAPI = async (projectId: number) => {
  const response = await apiRequest({
    method: "GET",
    url: `/projects/${projectId}/biddings`,
  });

  if (response.status === 200) {
    return response.data;
  } else if (response.status === 404) {
    toast.error("Project not found");
    return null;
  } else {
    toast.error(response.data.message || "Failed to retrieve biddings");
    return null;
  }
};



export const createAssignmentAPI = async (data: {
  project_id: number;
  freelancer_id: string;
}) => {
  const response = await apiRequest({
    method: "POST",
    url: "/assignments",
    data: {
      project_id: data.project_id,
      freelancer_id: data.freelancer_id,
    },
  });

  if (response.status === 201) {
    toast.success("Freelancer assigned successfully");
    return response.data;
  } else if (response.status === 422) {
    toast.error(response.data.message || "Validation error");
    return null;
  } else if (response.status === 401) {
    toast.error("Unauthorized. Please sign in as a client.");
    return null;
  } else if (response.status === 403) {
    toast.error("You don't own this project");
    return null;
  } else if (response.status === 404) {
    toast.error("No bid found from the selected freelancer for this project");
    return null;
  } else {
    toast.error(response.data.message || "Failed to assign freelancer");
    return null;
  }
};


export const getAssignmentAPI = async (projectId: number) => {
  const response = await apiRequest({
    method: "GET",
    url: `/assignments/${projectId}`,
  });

  if (response.status === 200) {
    return response.data;
  } else if (response.status === 404) {
    toast.error("Assignment not found for this project");
    return null;
  } else {
    toast.error(response.data.message || "Failed to retrieve assignment");
    return null;
  }
};


export const getMyBiddingsAPI = async () => {
  const response = await apiRequest({
    method: "GET",
    url: "/freelancer/biddings",
  });

  if (response.status === 200) {
    return response.data;
  } else if (response.status === 401) {
    toast.error("Unauthorized. Please sign in as a freelancer.");
    return null;
  } else {
    toast.error(response.data.message || "Failed to retrieve your biddings");
    return null;
  }
};


export const getMyAssignmentsAPI = async () => {
  const response = await apiRequest({
    method: "GET",
    url: "/freelancer/assignments",
  });

  if (response.status === 200) {
    return response.data;
  } else if (response.status === 401) {
    toast.error("Unauthorized. Please sign in as a freelancer.");
    return null;
  } else {
    toast.error(response.data.message || "Failed to retrieve your assignments");
    return null;
  }
};


export const updateAssignmentAPI = async (
  projectId: number,
  data: {
    status?: 'Assigned' | 'Submitted' | 'Completed' | 'Canceled';
    completion_date?: string | null;
    review_id?: number | null;
  }
) => {
  const response = await apiRequest({
    method: "PUT",
    url: `/assignments/${projectId}`,
    data: {
      status: data.status,
      completion_date: data.completion_date || null,
      review_id: data.review_id || null
    },
  });

  if (response.status === 200) {
    toast.success("Assignment updated successfully");
    return response.data;
  } else if (response.status === 422) {
    toast.error(response.data.message || "Validation error");
    return null;
  } else if (response.status === 401) {
    toast.error("Unauthorized");
    return null;
  } else if (response.status === 404) {
    toast.error("Assignment not found for this project");
    return null;
  } else if (response.status === 400) {
    toast.error("Client has insufficient balance");
    return null;
  } else {
    toast.error(response.data.message || "Failed to update assignment");
    return null;
  }
};
