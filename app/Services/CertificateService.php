<?php

namespace App\Services;

use App\Exceptions\BadRequestError;
use App\Exceptions\NotFoundError;
use App\Exceptions\ForbiddenError;
use App\Services\UserService;
use App\Models\Certificate;
use Dompdf\Dompdf;
use Dompdf\Options;

class CertificateService extends BaseService
{
    protected $model;
    protected $userService;
    protected $courseService;

    public function __construct(Certificate $certificateModel, UserService $userService, CourseService $courseService)
    {
        $this->model = $certificateModel;
        $this->userService = $userService;
        $this->courseService = $courseService;
    }

    public function checkCertificateExists($userId, $courseId)
    {
        $certificate = $this->model->where("user_id", $userId)->where("course_id", $courseId)->first();
        
        if ($certificate) {
            throw new BadRequestError("User already have this certificate");
        }
    }

    public function generate($userId, $courseId)
    {
        $this->checkCertificateExists($userId, $courseId);

        if (!$this->courseService->checkCourseComplete($userId, $courseId)) {
            throw new ForbiddenError("You must complete the course before generating a certificate.");
        }

        $user = $this->userService->getById($userId);
        $course = $this->courseService->get($courseId);

        $id = $this->generateId("certificate");

        // change this to use template in file
        $defaultTemplate = "blue_mountain.jpg";
        $templatePath = FCPATH . "uploads/certificate_templates/" . $defaultTemplate;

        $templateFile = file_exists($templatePath) ? $defaultTemplate : null;

        $html = view("certificates/template", [
            "user" => $user,
            "courseName" => $course["title"],
            'date' => date('F j, Y'),
            "certificateId" => $id,
            "templateFile" => $templateFile
        ]);

        $options = new Options();
        $options->set("isRemoteEnabled", true);
        $dompdf = new Dompdf($options);

        $dompdf->setPaper("A4", "landscape");

        $dompdf->loadHtml($html);
        $dompdf->render();

        $output = $dompdf->output();
        $filename = $id . ".pdf";
        $filepath = WRITEPATH . "certificates/" . $filename;

        if(!is_dir(WRITEPATH . "certificates/")) {
            mkdir(WRITEPATH . "certificates/", 0755, true);
        }

        file_put_contents($filepath, $output);

        $downloadLink = base_url("api/certificates/download/" . $filename);
        $data = [
            "id" => $id,
            "user_id" => $userId,
            "course_id" => $courseId,
            "pdf_url" => $downloadLink
        ];

        $this->model->insert($data);

        return [
            'downloadLink' => $downloadLink,
            'id' => $id
        ];
    }

    public function uploadTemplate($file)
    {
        if (!$file->isValid()) {
            throw new \RuntimeException("Invalid file upload");
        }

        $filename = $file->getRandomName();

        $destination = FCPATH . "uploads/certificate_templates";

        if(!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);

        return $filename;
    }

    public function getOne($id)
    {
        $certificate = $this->model
        ->select("certificates.*, users.name as user_name")
        ->join("users", "users.id = certificates.user_id")
        ->find($id);

        if (!$certificate) {
            throw new NotFoundError("Certificate not found. Invalid certificate id");
        }

        return $certificate;
    }

    public function getAll($name = null, $email = null)
    {
        $builder = $this->model
        ->select("certificates.*, users.name as user_name")
        ->join("users", "users.id = certificates.user_id");

        if ($name) {
            $builder->like("users.name", $name);
        }

        if ($email) {
            $builder->like("users.email", $email);
        }

        $certificates = $builder->findAll();

        if (empty($certificates)) {
            return [];
        }

        return $certificates;
    }

    // public function delete($id)
    // {
    //     $certificate = $this->getOne($id);

    //     $filepath = WRITEPATH . "certificates/" . $certificate["id"] . ".pdf";

    //     if (file_exists($filepath)) {
    //         unlink($filepath);
    //     }

    //     $this->model->delete($id);
    // }

    // public function verifyCertificateAccess($id)
    // {

    // }
}