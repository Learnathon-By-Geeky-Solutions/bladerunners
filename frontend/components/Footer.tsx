import React from "react";

export default function Footer() {
  const data = [
    {
      title: "For Clients",
      options: [
        { link: "#", text: "How to hire" },
        { link: "#", text: "Talent Marketplace" },
        { link: "#", text: "Project Catalog" },
        { link: "#", text: "Hire an agency" },
        { link: "#", text: "Contact to hire" },
        { link: "#", text: "Direct Contracts" },
      ],
    },
    {
      title: "For Talent",
      options: [
        { link: "#", text: "How to find work" },
        { link: "#", text: "Direct Contracts" },
        { link: "#", text: "Find jobs worldwide" },
        { link: "#", text: "Find jobs in Bangladesh" },
        { link: "#", text: "Exclusive resources" },
      ],
    },
    {
      title: "Resources",
      options: [
        { link: "#", text: "Help and Supports" },
        { link: "#", text: "Success stories" },
        { link: "#", text: "Reviews" },
        { link: "#", text: "Resources" },
        { link: "#", text: "Blog" },
      ],
    },
    {
      title: "Company",
      options: [
        { link: "#", text: "About Us" },
        { link: "#", text: "Leadership" },
        { link: "#", text: "Investors" },
        { link: "#", text: "Careers" },
        { link: "#", text: "Contact Us" },
        { link: "#", text: "partners" },
      ],
    },
  ];

  return (
    <div className="flex justify-between px-20 py-10 bg-gray-200">
      {data.map((section) => (
        <div key={section.title} className="flex flex-col gap-4">
          <h3 className="font-bold text-lg">{section.title}</h3>
          <div className="flex flex-col gap-2">
            {section.options.map((option) => (
              <a
                key={`${section.title}-${option.text}`}
                href={option.link}
                className="text-gray-600 hover:text-gray-900"
              >
                {option.text}
              </a>
            ))}
          </div>
        </div>
      ))}
    </div>
  );
}
