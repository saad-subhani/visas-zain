export type Course = {
  courseTitle: string;
  slug: string;
  description: string;

  level:
    | "Bachelor"
    | "Master"
    | "PhD";

  field: string;

  country: string;
  language: string;
  intake: string;
  duration: string;
  tuitionFee: string;

  admissionRequirements: string;

  status: "Active" | "Inactive";
};

export const courses: Course[] = [
  {
    courseTitle: "BSc Computer Science",
    slug: "bsc-computer-science",

    description:
      "Build a strong foundation in computer science, software development, programming and modern technology through an internationally focused undergraduate programme.",

    level: "Bachelor",

    field: "Computer Science",

    country: "United Kingdom",
    language: "English",
    intake: "September, January",
    duration: "3 Years",
    tuitionFee: "£18,000 – £30,000 per year",

    admissionRequirements:
      "Applicants should have a recognised secondary or higher secondary qualification with suitable academic results. English language proficiency such as IELTS or an accepted equivalent may be required.",

    status: "Active",
  },

  {
    courseTitle: "BSc Business Management",
    slug: "bsc-business-management",

    description:
      "Develop practical knowledge of business, management, leadership and entrepreneurship while preparing for an international career in the business world.",

    level: "Bachelor",

    field: "Business & Management",

    country: "Australia",
    language: "English",
    intake: "February, July",
    duration: "3 Years",
    tuitionFee: "AUD 28,000 – 45,000 per year",

    admissionRequirements:
      "Applicants must meet the academic entry requirements of the selected institution. English language proficiency through IELTS, PTE, TOEFL or an accepted equivalent may be required.",

    status: "Active",
  },

  {
    courseTitle: "MSc Data Science",
    slug: "msc-data-science",

    description:
      "Develop advanced skills in data analysis, machine learning, statistics and modern data technologies through a specialised postgraduate programme.",

    level: "Master",

    field: "Data Science",

    country: "Canada",
    language: "English",
    intake: "September, January",
    duration: "1 – 2 Years",
    tuitionFee: "CAD 25,000 – 45,000 per year",

    admissionRequirements:
      "Applicants normally require a relevant bachelor's degree and may need to demonstrate academic, technical and English language proficiency requirements set by the institution.",

    status: "Active",
  },

  {
    courseTitle: "Master of Business Administration",
    slug: "master-of-business-administration",

    description:
      "Strengthen your leadership, strategy and management capabilities through an internationally focused MBA programme designed for ambitious professionals.",

    level: "Master",

    field: "Business & Management",

    country: "United States",
    language: "English",
    intake: "August, January",
    duration: "1 – 2 Years",
    tuitionFee: "USD 30,000 – 60,000 per year",

    admissionRequirements:
      "Applicants generally require a recognised bachelor's degree. Some MBA programmes may require professional work experience, GMAT or GRE scores and English language proficiency.",

    status: "Active",
  },

  {
    courseTitle: "PhD in Engineering",
    slug: "phd-in-engineering",

    description:
      "Pursue advanced research in engineering and contribute to innovation through a research-intensive doctoral programme at an international institution.",

    level: "PhD",

    field: "Engineering",

    country: "United Kingdom",
    language: "English",
    intake: "September, January",
    duration: "3 – 4 Years",
    tuitionFee: "£20,000 – £35,000 per year",

    admissionRequirements:
      "Applicants generally require a relevant master's degree or equivalent academic qualification, a suitable research proposal and evidence of academic and English language proficiency.",

    status: "Active",
  },

  {
    courseTitle: "MSc International Business",
    slug: "msc-international-business",

    description:
      "Explore global markets, international strategy, management and cross-cultural business practices through a specialised master's programme.",

    level: "Master",

    field: "International Business",

    country: "Germany",
    language: "English",
    intake: "October, April",
    duration: "1.5 – 2 Years",
    tuitionFee: "€8,000 – €20,000 per year",

    admissionRequirements:
      "Applicants should hold a recognised bachelor's degree in a relevant discipline and satisfy the institution's academic and English language requirements.",

    status: "Active",
  },
];