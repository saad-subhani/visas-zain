export type Scholarship = {
  scholarshipTitle: string;
  slug: string;
  description: string;
  scholarshipAmount: string;
  applicationDeadline: string;
  eligibleCountries: string[];
  studyLevel:
    | "Undergraduate"
    | "Postgraduate"
    | "Masters"
    | "PhD"
    | "All Levels";
  requirements: string;
  officialSource: string;
  howToApply: string;
  status: "Active" | "Inactive";
};

export const scholarships: Scholarship[] = [
  {
    scholarshipTitle: "Chevening Scholarships",
    slug: "chevening-scholarships",
    description:
      "A fully funded scholarship programme for outstanding students who want to pursue a master's degree in the United Kingdom.",
    scholarshipAmount: "Fully Funded",
    applicationDeadline: "November",
    eligibleCountries: [
      "Pakistan",
      "United Kingdom",
      "International Students",
    ],
    studyLevel: "Masters",
    requirements:
      "Applicants should have an undergraduate degree, relevant professional experience and meet the programme's eligibility requirements.",
    officialSource: "https://www.chevening.org/",
    howToApply:
      "Complete the online application through the official Chevening website and provide all required supporting documents before the deadline.",
    status: "Active",
  },
  {
    scholarshipTitle: "Commonwealth Master's Scholarships",
    slug: "commonwealth-masters-scholarships",
    description:
      "Scholarship opportunities supporting students from eligible Commonwealth countries to study master's programmes in the United Kingdom.",
    scholarshipAmount: "Fully Funded",
    applicationDeadline: "Varies",
    eligibleCountries: [
      "Pakistan",
      "Commonwealth Countries",
    ],
    studyLevel: "Masters",
    requirements:
      "Applicants must meet the academic, nationality and programme-specific eligibility requirements defined by the scholarship scheme.",
    officialSource: "https://cscuk.fcdo.gov.uk/",
    howToApply:
      "Applications are submitted through the relevant national nominating agency or official application route.",
    status: "Active",
  },
  {
    scholarshipTitle: "Australia Awards Scholarships",
    slug: "australia-awards-scholarships",
    description:
      "Long-term development awards that provide opportunities for students from participating countries to undertake study in Australia.",
    scholarshipAmount: "Fully Funded",
    applicationDeadline: "Varies by country",
    eligibleCountries: [
      "Pakistan",
      "Eligible Developing Countries",
    ],
    studyLevel: "Postgraduate",
    requirements:
      "Applicants must satisfy citizenship, academic, professional and programme-specific eligibility requirements.",
    officialSource: "https://www.australiaawards.gov.au/",
    howToApply:
      "Check the official Australia Awards website for your country and submit an application through the specified application process.",
    status: "Active",
  },
  {
    scholarshipTitle: "University Scholarships",
    slug: "university-scholarships",
    description:
      "A range of scholarships and financial support opportunities offered directly by universities to international students.",
    scholarshipAmount: "Varies by University",
    applicationDeadline: "Varies",
    eligibleCountries: [
      "International Students",
    ],
    studyLevel: "All Levels",
    requirements:
      "Eligibility depends on the university, programme, academic record and individual scholarship requirements.",
    officialSource: "https://www.studywithus.com/",
    howToApply:
      "Review the scholarship requirements of your selected university and submit the required application or supporting documents.",
    status: "Active",
  },
];