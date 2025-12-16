<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $myCourses = Course::where('user_id', $user->id)->get();
        $matches = $this->findMatches($user->id);
        
        return view('courses.index', compact('myCourses', 'matches'));
    }
    
    private function findMatches($userId)
    {
        $classmates = DB::select("
            SELECT DISTINCT
                other_users.id as user_id,
                other_users.name as user_name,
                other_courses.class_code,
                other_courses.subject,
                other_courses.section,
                other_courses.professor,
                my_courses.section as my_section,
                'classmate' as match_type,
                ( 
                    CASE 
                        WHEN other_courses.section = my_courses.section THEN 100
                        WHEN SUBSTRING(other_courses.section, 1, 2) = SUBSTRING(my_courses.section, 1, 2) THEN 80
                        WHEN SUBSTRING(other_courses.section, 1, 1) = SUBSTRING(my_courses.section, 1, 1) THEN 60
                        ELSE 40
                    END
                ) as relevance_score
            
            FROM courses as my_courses
            INNER JOIN courses as other_courses
                ON my_courses.class_code = other_courses.class_code
                AND my_courses.school_year = other_courses.school_year
                AND my_courses.semester = other_courses.semester
                AND other_courses.user_id != ?
            INNER JOIN users as other_users ON other_courses.user_id = other_users.id
            WHERE my_courses.user_id = ?
                AND my_courses.deleted_at IS NULL
                AND other_courses.deleted_at IS NULL
            ORDER BY relevance_score DESC, other_users.name ASC
        ", [$userId, $userId]);
        
        $sameSubjectOrProf = DB::select("
            SELECT DISTINCT
                other_users.id as user_id,
                other_users.name as user_name,
                other_courses.class_code,
                other_courses.subject,
                other_courses.section,
                other_courses.professor,
                my_courses.subject as my_subject,
                my_courses.professor as my_professor,
                CASE 
                    WHEN LOWER(other_courses.subject) = LOWER(my_courses.subject) 
                         AND other_courses.professor = my_courses.professor 
                         AND other_courses.professor != '' 
                    THEN 'subject & professor'
                    WHEN LOWER(other_courses.subject) = LOWER(my_courses.subject) 
                    THEN 'subject'
                    WHEN other_courses.professor = my_courses.professor 
                         AND other_courses.professor != '' 
                    THEN 'professor'
                END as match_type,
                (
                    CASE WHEN LOWER(other_courses.subject) = LOWER(my_courses.subject) THEN 50 ELSE 0 END +
                    CASE WHEN other_courses.professor = my_courses.professor AND other_courses.professor != '' THEN 30 ELSE 0 END
                ) as relevance_score
            FROM courses as my_courses
            INNER JOIN courses as other_courses
                ON other_courses.user_id != ?
                AND (
                    LOWER(other_courses.subject) = LOWER(my_courses.subject)
                    OR (other_courses.professor = my_courses.professor AND other_courses.professor != '')
                )
            INNER JOIN users as other_users ON other_courses.user_id = other_users.id
            WHERE my_courses.user_id = ?
                AND my_courses.deleted_at IS NULL
                AND other_courses.deleted_at IS NULL
                AND NOT EXISTS (
                    SELECT 1 FROM courses as check_courses
                    WHERE check_courses.user_id = other_courses.user_id
                        AND check_courses.class_code = my_courses.class_code
                        AND check_courses.school_year = my_courses.school_year
                        AND check_courses.semester = my_courses.semester
                        AND check_courses.deleted_at IS NULL
                )
            HAVING relevance_score > 0
            ORDER BY relevance_score DESC, other_users.name ASC
        ", [$userId, $userId]);
        
        return [
            'classmates' => $classmates,
            'same' => $sameSubjectOrProf
        ];
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_code' => 'required',
            'school_year' => 'required',
            'semester' => 'required',
            'subject' => 'required',
            'section' => 'required',
            'professor' => 'nullable',
        ]);
        
        $validated['user_id'] = auth()->id();
        Course::create($validated);
        
        return redirect()->route('courses.index')->with('success', '🟢 Class details created.');
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'class_code' => 'required',
            'school_year' => 'required',
            'semester' => 'required',
            'subject' => 'required',
            'section' => 'required',
            'professor' => 'nullable',
        ]);

        $course = Course::findOrFail($id);
        
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }
        
        $course->update($validated);
        return redirect()->route('courses.index')->with('success', '🟠 Class details updated.');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }
        
        $course->delete();
        return redirect()->route('courses.index')->with('success', '🔴 Class details deleted.');
    }
}