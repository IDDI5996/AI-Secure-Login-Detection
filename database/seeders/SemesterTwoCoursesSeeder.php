<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class SemesterTwoCoursesSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            // Core courses
            ['code' => 'IA 429', 'name' => 'Information Systems Forensics Internal Auditing', 'credits' => 9.0, 'type' => 'core'],
            ['code' => 'CS 428', 'name' => 'Cyber Security and Digital Forensics', 'credits' => 9.0, 'type' => 'core'],
            ['code' => 'IA 422', 'name' => 'Ethical Hacking', 'credits' => 9.0, 'type' => 'core'],
            ['code' => 'IA 428', 'name' => 'Trust Management in E-Commerce', 'credits' => 7.5, 'type' => 'core'],
            ['code' => 'IA 423', 'name' => 'Wireless Security', 'credits' => 9.0, 'type' => 'core'],
            ['code' => 'IA 421', 'name' => 'Business Continuity and Disaster Recovery', 'credits' => 9.0, 'type' => 'core'],
            // Electives
            ['code' => 'IA 424', 'name' => 'Selected Topics in Cyber Security and Digital Forensics Engineering', 'credits' => 7.5, 'type' => 'elective'],
            ['code' => 'CP 327', 'name' => 'Systems Programming', 'credits' => 7.5, 'type' => 'elective'],
            ['code' => 'CG 321', 'name' => 'Blockchain Technology', 'credits' => 7.5, 'type' => 'elective'],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['code' => $course['code']],
                [
                    'name' => $course['name'],
                    'semester' => 'Semester 2',
                    'year' => 4,
                    'credits' => $course['credits'],
                    'type' => $course['type'],
                ]
            );
        }
    }
}