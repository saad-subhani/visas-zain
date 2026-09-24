// data/destinations.ts

export type Destination = {
  countryName: string;
  slug: string;
  description: string;
  whyStudy: string;
  tuitionRange: string;
  livingCost: string;
  visaInformation: string;
  image: string;
  status: "Active" | "Inactive";
};

export const destinations: Destination[] = [
  {
    countryName: "United Kingdom",
    slug: "united-kingdom",
    description:
      "Study at world-class universities, experience a globally recognised education system and build a strong foundation for your international career.",
    whyStudy:
      "The United Kingdom offers a diverse range of internationally recognised universities and academic programs. Students can experience a multicultural environment while gaining valuable academic and professional exposure. From undergraduate degrees to postgraduate study, the UK provides multiple pathways for students looking to develop their skills and future opportunities.",
    tuitionRange:
      "£12,000 – £30,000 per year",
    livingCost:
      "£10,000 – £15,000 per year",
    visaInformation:
      "International students generally require a Student visa to study in the UK. Requirements can include a Confirmation of Acceptance for Studies (CAS), proof of funds and other supporting documentation.",
    image:
      "/images/services/pre-departure.jpg",
    status: "Active",
  },

  {
    countryName: "United States",
    slug: "united-states",
    description:
      "Explore leading universities, flexible academic pathways and a wide range of opportunities across one of the world's most influential education systems.",
    whyStudy:
      "The United States offers a broad selection of universities, programs and academic pathways. Students can explore different fields, participate in diverse campus communities and gain exposure to an international academic environment.",
    tuitionRange:
      "$20,000 – $50,000 per year",
    livingCost:
      "$12,000 – $20,000 per year",
    visaInformation:
      "International students generally require an F-1 student visa for academic study. Students must meet the relevant admission, documentation and financial requirements before applying.",
    image:
      "/images/services/pre-departure.jpg",
    status: "Active",
  },

  {
    countryName: "Canada",
    slug: "canada",
    description:
      "Discover quality education, welcoming communities and internationally respected institutions while preparing for your next chapter abroad.",
    whyStudy:
      "Canada is known for its diverse educational institutions and multicultural environment. Students can choose from universities, colleges and specialised programs while experiencing life in a globally connected country.",
    tuitionRange:
      "CAD 15,000 – CAD 35,000 per year",
    livingCost:
      "CAD 12,000 – CAD 20,000 per year",
    visaInformation:
      "International students generally require a Canadian study permit. Applicants may need an acceptance letter, proof of financial support and other required documents.",
    image:
      "/images/services/pre-departure.jpg",
    status: "Active",
  },

  {
    countryName: "Australia",
    slug: "australia",
    description:
      "Experience innovative education, practical learning and an international student environment designed to open doors to global opportunities.",
    whyStudy:
      "Australia offers a wide range of internationally recognised institutions and practical academic programs. Students can experience a multicultural environment while developing academic and professional skills.",
    tuitionRange:
      "AUD 20,000 – AUD 45,000 per year",
    livingCost:
      "AUD 24,000 – AUD 30,000 per year",
    visaInformation:
      "International students generally require a Student visa to study in Australia. Requirements may include enrolment evidence, financial capacity and other supporting documentation.",
    image:
      "/images/services/pre-departure.jpg",
    status: "Active",
  },
];