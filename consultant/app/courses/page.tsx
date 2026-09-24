import Link from "next/link";
import type { Metadata } from "next";
import type { CSSProperties } from "react";

import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";

import styles from "./page.module.css";

export const metadata: Metadata = {
  title: "Courses | Consultant",
  description:
    "Explore international study programmes, courses and degree opportunities with Consultant.",
};

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

type CoursesResponse = {
  success: boolean;
  count: number;
  data: ApiCourse[];
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

async function getCourses(): Promise<Course[]> {
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

    return result.data.map(normalizeCourse);
  } catch {
    return [];
  }
}

export default async function CoursesPage() {
  const courses = await getCourses();

  const activeCourses = courses.filter(
    (course) => course.status === "Active"
  );

  return (
    <>
      <Navbar />

      <main className={styles.page}>
        {/* HERO */}
        <section className={styles.hero}>
          <div className={styles.heroImage}></div>
          <div className={styles.heroOverlay}></div>

          <div
            className={`${styles.heroGlow} ${styles.heroGlowOne}`}
          ></div>

          <div
            className={`${styles.heroGlow} ${styles.heroGlowTwo}`}
          ></div>

          <div className={styles.heroContent}>
            <div className={styles.breadcrumb}>
              <Link href="/">HOME</Link>
              <span>/</span>
              <span>COURSES</span>
            </div>

            <span className={styles.eyebrow}>
              STUDY PROGRAMMES
            </span>

            <h1>
              FIND THE
              <br />
              <span>RIGHT COURSE.</span>
            </h1>

            <p>
              Explore internationally recognised study
              programmes and find a course that matches
              your academic goals, interests and future
              ambitions.
            </p>

            <Link
              href="#courses"
              className={styles.heroButton}
            >
              EXPLORE COURSES
              <span className={styles.buttonArrow}>↓</span>
            </Link>
          </div>

          <div className={styles.heroLine}></div>
        </section>

        {/* COURSES */}
        <section
          className={styles.courses}
          id="courses"
        >
          <div className={styles.backgroundGlow}></div>

          <div className={styles.container}>
            <div className={styles.sectionIntro}>
              <div>
                <span className={styles.sectionEyebrow}>
                  EXPLORE PROGRAMMES
                </span>

                <h2>
                  CHOOSE YOUR
                  <br />
                  DIRECTION.
                </h2>
              </div>

              <p>
                Explore our available study programmes
                across different academic levels, fields
                and international destinations.
              </p>
            </div>

            <div className={styles.courseGrid}>
              {activeCourses.map((course, index) => (
                <article
                  className={styles.courseCard}
                  key={course.slug}
                  style={
                    {
                      "--delay": `${index * 0.08}s`,
                    } as CSSProperties
                  }
                >
                  <div className={styles.cardGlow}></div>

                  <div className={styles.cardTop}>
                    <span className={styles.number}>
                      {String(index + 1).padStart(2, "0")}
                    </span>

                    <span className={styles.level}>
                      {course.level}
                    </span>
                  </div>

                  <div className={styles.cardContent}>
                    <span className={styles.field}>
                      {course.field}
                    </span>

                    <h3>{course.courseTitle}</h3>

                    <p>{course.description}</p>
                  </div>

                  <div className={styles.cardMeta}>
                    <div>
                      <span>COUNTRY</span>
                      <strong>{course.country}</strong>
                    </div>

                    <div>
                      <span>DURATION</span>
                      <strong>{course.duration}</strong>
                    </div>

                    <div>
                      <span>INTAKE</span>
                      <strong>{course.intake}</strong>
                    </div>
                  </div>

                  <Link
                    href={`/courses/${course.slug}`}
                    className={styles.viewButton}
                  >
                    VIEW COURSE
                    <span>↗</span>
                  </Link>
                </article>
              ))}
            </div>
          </div>
        </section>

        {/* CTA */}
        <section className={styles.cta}>
          <div className={styles.ctaBox}>
            <div className={styles.ctaGlow}></div>

            <span className={styles.ctaEyebrow}>
              NEED HELP CHOOSING?
            </span>

            <h2>
              LET'S FIND YOUR
              <br />
              RIGHT COURSE.
            </h2>

            <p>
              Not sure which programme or destination is
              right for you? Get personalised guidance
              based on your academic background and
              future goals.
            </p>

            <Link
              href="/#contact"
              className={styles.ctaButton}
            >
              GET GUIDANCE
              <span>↗</span>
            </Link>
          </div>
        </section>
      </main>

      <Footer />
    </>
  );
}