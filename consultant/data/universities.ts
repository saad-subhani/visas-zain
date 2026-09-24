export type University = {
  universityName: string;
  slug: string;
  country: string;
  city: string;
  description: string;
  programmes: string[];
  tuitionFee: string;
  intakeDates: string;
  generalRequirements: string;
  englishRequirements: string;
  scholarshipsAvailable: boolean;
  officialWebsite: string;
  image: string;
  status: "Active" | "Inactive";
};

export const universities: University[] = [
  {
    universityName: "University of Manchester",
    slug: "university-of-manchester",
    country: "United Kingdom",
    city: "Manchester",
    description:
      "A globally recognised university offering a wide range of undergraduate and postgraduate programmes with a strong focus on research, innovation and academic excellence.",
    programmes: [
      "Business & Management",
      "Computer Science",
      "Engineering",
      "Medicine",
      "Social Sciences",
    ],
    tuitionFee: "£20,000 – £35,000 per year",
    intakeDates: "September, January",
    generalRequirements:
      "Academic qualifications relevant to the selected programme. Specific entry requirements vary by course.",
    englishRequirements:
      "IELTS, TOEFL or an accepted equivalent English language qualification.",
    scholarshipsAvailable: true,
    officialWebsite: "https://www.manchester.ac.uk/",
    image: "/images/services/pre-departure.jpg",
    status: "Active",
  },

  {
    universityName: "University of Toronto",
    slug: "university-of-toronto",
    country: "Canada",
    city: "Toronto",
    description:
      "One of Canada's leading universities, offering diverse academic programmes, research opportunities and a vibrant international student environment.",
    programmes: [
      "Business",
      "Computer Science",
      "Engineering",
      "Life Sciences",
      "Arts & Humanities",
    ],
    tuitionFee: "CAD 35,000 – 60,000 per year",
    intakeDates: "September, January, May",
    generalRequirements:
      "Applicants must meet the academic requirements for their selected programme and provide the required supporting documents.",
    englishRequirements:
      "IELTS, TOEFL or another accepted English language qualification may be required.",
    scholarshipsAvailable: true,
    officialWebsite: "https://www.utoronto.ca/",
    image: "/images/services/pre-departure.jpg",
    status: "Active",
  },

  {
    universityName: "Monash University",
    slug: "monash-university",
    country: "Australia",
    city: "Melbourne",
    description:
      "A leading Australian university known for its international community, industry connections and broad range of study opportunities.",
    programmes: [
      "Business",
      "Information Technology",
      "Engineering",
      "Health Sciences",
      "Education",
    ],
    tuitionFee: "AUD 30,000 – 55,000 per year",
    intakeDates: "February, July",
    generalRequirements:
      "Academic entry requirements depend on the selected programme and previous qualification.",
    englishRequirements:
      "IELTS, TOEFL, PTE or an accepted equivalent may be required depending on the programme.",
    scholarshipsAvailable: true,
    officialWebsite: "https://www.monash.edu/",
    image: "/images/services/pre-departure.jpg",
    status: "Active",
  },

  {
    universityName: "Arizona State University",
    slug: "arizona-state-university",
    country: "United States",
    city: "Tempe",
    description:
      "A major American university offering extensive undergraduate and graduate study options across technology, business, engineering and many other disciplines.",
    programmes: [
      "Business",
      "Computer Science",
      "Engineering",
      "Data Science",
      "Social Sciences",
    ],
    tuitionFee: "USD 30,000 – 45,000 per year",
    intakeDates: "August, January, May",
    generalRequirements:
      "Applicants must meet the academic and programme-specific admission requirements.",
    englishRequirements:
      "IELTS, TOEFL, PTE or another accepted English language qualification.",
    scholarshipsAvailable: true,
    officialWebsite: "https://www.asu.edu/",
    image: "/images/services/pre-departure.jpg",
    status: "Active",
  },
];