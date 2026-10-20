<?php

namespace Api\Courses;

use Core\BaseController;
use Models\Courses;

class CoursesController extends BaseController
{
    private $Courses;

    public function __construct()
    {
        $this->Courses = new Courses();
    }

    public function all()
    {
        $this->json([
            'data' => $this->Courses->find()
        ]);
    }

    public function add()
    {
        $data = $_POST;

        if (empty($data['title'])) {
            $this->json([
                'status' => 'error',
                'message' => 'Title is required.'
            ]);
        }

        if ($this->Courses->save($data)) {
            $this->json([
                'status' => 'success',
                'message' => empty($data['id'])
                    ? 'Course added successfully.'
                    : 'Course updated successfully.'
            ]);
        }

        $this->json([
            'status' => 'error',
            'message' => 'Unable to save course.'
        ]);
    }

    public function edit($id)
    {
         $course = $this->Courses->findByID($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = $_POST;

            if (empty($data['title'])) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Title is required.'
                ]);
            }

            if ($this->Courses->save($data)) {
                $this->json([
                    'status' => 'success',
                    'message' => 'Course updated successfully.'
                ]);
            }

            $this->json([
                'status' => 'error',
                'message' => 'Unable to save course.'
            ]);
        }
        if (!$course) {
            $this->json([
                'status' => 'error',
                'message' => 'Course not found.'
            ]);
        }

        $this->json($course);
        
    }

    public function delete($id)
    {
        if (empty($id)) {
            $this->json([
                'status' => 'error',
                'message' => 'Course ID is required.'
            ]);
        }

        if ($this->Courses->delete($id)) {
            $this->json([
                'status' => 'success',
                'message' => 'Course deleted successfully.'
            ]);
        }

        $this->json([
            'status' => 'error',
            'message' => 'Unable to delete course.'
        ]);
    }
}