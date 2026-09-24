import Link from "next/link";
import { notFound } from "next/navigation";
import type { Metadata } from "next";

import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";

import styles from "./page.module.css";

const API_URL = "http://localhost/realCMS/api/courses.php";

type Course = {
  id: number;
  courseTitle: string;
  slug: string;
  description: string;
  level: string;
  field: string;
  country: string;
  language: string;
  intake: string;
  tuitionFee: string;
  duration: string;
  admissionRequirements: string;
  status: "Active" | "Inactive";
};

type ApiCourse = {
  id: number;
  title: string;
  slug: string;
  description: string;
  level: string;
  field: string;
  country: string;
  language: string;
  intake: string;
  tuition_fee: string;
  duration: string;
  requirements: string;
  status: string;
};

type CourseResponse = {
  success: boolean;
  data: ApiCourse;
};

type CoursesResponse = {
  success: boolean;
  count: number;
  data: ApiCourse[];
};

type Props = {
  params: Promise<{
    slug: string;
  }>;
};

function normalizeCourse(course: ApiCourse): Course {
  return {
    id: course.id,
    courseTitle: course.title,
    slug: course.slug,
    description: course.description,
    level: course.level,
    field: course.field,
    country: course.country,
    language: course.language,
    intake: course.intake,
    tuitionFee: course.tuition_fee,
    duration: course.duration,
    admissionRequirements: course.requirements,
    status:
      course.status.toLowerCase() === "active"
        ? "Active"
        : "Inactive",
  };
}

async function getCourse(
  slug: string
): Promise<Course | null> {
  try {
    const response = await fetch(
      `${API_URL}?slug=${encodeURIComponent(slug)}`,
      {
        cache: "no-store",
      }
    );

    if (!response.ok) {
      return null;
    }

    const result: CourseResponse =
      await response.json();

    if (!result.success || !result.data) {
      return null;
    }

    return normalizeCourse(result.data);
  } catch {
    return null;
  }
}

async function getCourseSlugs(): Promise<string[]> {
  try {
    const response = await fetch(API_URL, {
      cache: "no-store",
    });

    if (!response.ok) {
      return [];
    }

    const result: CoursesResponse =
      await response.json();

    if (
      !result.success ||
      !Array.isArray(result.data)
    ) {
      return [];
    }

    return result.data.map(
      (course) => course.slug
    );
  } catch {
    return [];
  }
}

export async function generateStaticParams() {
  const slugs = await getCourseSlugs();

  return slugs.map((slug) => ({
    slug,
  }));
}

export async function generateMetadata({
  params,
}: Props): Promise<Metadata> {
  const { slug } = await params;

  const course = await getCourse(slug);

  if (!course) {
    return {
      title: "Course Not Found | Consultant",
    };
  }

  return {
    title: `${course.courseTitle} | Consultant`,
    description: course.description,
  };
}

export default async function CourseDetailPage({
  params,
}: Props) {
  const { slug } = await params;

  const course = await getCourse(slug);

  if (!course || course.status !== "Active") {
    notFound();
  }

  return (
    <>
      <Navbar />

      <main className={styles.page}>
        {/* HERO */}
        <section className={styles.hero}>
          <div className={styles.heroOverlay}></div>

          <div className={styles.heroContent}>
            <span className={styles.eyebrow}>
              STUDY PROGRAMME
            </span>

            <h1>{course.courseTitle}</h1>

            <p>{course.description}</p>

            <Link
              href="#details"
              className={styles.heroButton}
            >
              EXPLORE DETAILS
              <span>↓</span>
            </Link>
          </div>
        </section>

        {/* DETAILS */}
        <section
          className={styles.details}
          id="details"
        >
          <div className={styles.container}>
            <div className={styles.sectionIntro}>
              <div>
                <span className={styles.sectionEyebrow}>
                  COURSE DETAILS
                </span>

                <h2>
                  BUILD YOUR
                  <br />
                  FUTURE.
                </h2>
              </div>

              <p>
                Explore the study level, destination,
                duration, tuition and admission
                requirements for this programme.
              </p>
            </div>

            {/* INFO */}
            <div className={styles.infoGrid}>
              <div className={styles.infoCard}>
                <span>LEVEL</span>
                <strong>{course.level}</strong>
              </div>

              <div className={styles.infoCard}>
                <span>FIELD</span>
                <strong>{course.field}</strong>
              </div>

              <div className={styles.infoCard}>
                <span>COUNTRY</span>
                <strong>{course.country}</strong>
              </div>

              <div className={styles.infoCard}>
                <span>LANGUAGE</span>
                <strong>{course.language}</strong>
              </div>

              <div className={styles.infoCard}>
                <span>INTAKE</span>
                <strong>{course.intake}</strong>
              </div>

              <div className={styles.infoCard}>
                <span>DURATION</span>
                <strong>{course.duration}</strong>
              </div>

              <div className={styles.infoCard}>
                <span>TUITION FEE</span>
                <strong>{course.tuitionFee}</strong>
              </div>

              <div className={styles.infoCard}>
                <span>STATUS</span>
                <strong className={styles.active}>
                  {course.status}
                </strong>
              </div>
            </div>

            {/* REQUIREMENTS */}
            <div className={styles.requirements}>
              <div>
                <span className={styles.blockEyebrow}>
                  ADMISSION REQUIREMENTS
                </span>

                <h3>
                  WHAT YOU
                  <br />
                  NEED.
                </h3>
              </div>

              <p>{course.admissionRequirements}</p>
            </div>

            {/* CTA */}
            <div className={styles.cta}>
              <span className={styles.ctaEyebrow}>
                READY TO START?
              </span>

              <h2>
                YOUR FUTURE
                <br />
                STARTS HERE.
              </h2>

              <p>
                Get personalised guidance about this
                course, university options and your
                application journey.
              </p>

              <Link
                href="/#contact"
                className={styles.ctaButton}
              >
                GET GUIDANCE
                <span>↗</span>
              </Link>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </>
  );
}