<?php

namespace Api\Students;

use Core\BaseController;
use Models\Students;

class StudentsController extends BaseController
{
    private $Students;

    public function __construct()
    {
        $this->Students = new Students();
    }

    public function all()
    {
        $this->json([
            'data' => $this->Students->find()
        ]);
    }

    public function add()
    {
        $data = $_POST;

        if (empty($data['lastname']) || empty($data['firstname'])) {
            $this->json([
                'status' => 'error',
                'message' => 'Last name and first name are required.'
            ]);
        }

        if ($this->Students->save($data)) {
            $this->json([
                'status' => 'success',
                'message' => empty($data['id'])
                    ? 'Student added successfully.'
                    : 'Student updated successfully.'
            ]);
        }

        $this->json([
            'status' => 'error',
            'message' => 'Unable to save student.'
        ]);
    }

    public function edit($id)
    {
         $student = $this->Students->findByID($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $data = $_POST;

            if (empty($data['lastname']) || empty($data['firstname'])) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Last name and first name are required.'
                ]);
            }

            if ($this->Students->save($data)) {
                $this->json([
                    'status' => 'success',
                    'message' => 'Student updated successfully.'
                ]);
            }

            $this->json([
                'status' => 'error',
                'message' => 'Unable to save student.'
            ]);
        }
        if (!$student) {
            $this->json([
                'status' => 'error',
                'message' => 'Student not found.'
            ]);
        }

        $this->json($student);
        
    }

    public function delete($id)
    {
        if (empty($id)) {
            $this->json([
                'status' => 'error',
                'message' => 'Student ID is required.'
            ]);
        }

        if ($this->Students->delete($id)) {
            $this->json([
                'status' => 'success',
                'message' => 'Student deleted successfully.'
            ]);
        }

        $this->json([
            'status' => 'error',
            'message' => 'Unable to delete student.'
        ]);
    }
}