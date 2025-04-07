<?php

namespace App\Services;

use App\Exceptions\BadRequestError;
use App\Services\UserService;
use App\Models\Certificate;
use Dompdf\Dompdf;
use Dompdf\Options;

class CertificateService extends BaseService
{
    protected $model;
    protected $userService;
    protected $enrollmentService;

    public function __construct(Certificate $certificateModel, UserService $userService, EnrollmentService $enrollmentService)
    {
        $this->model = $certificateModel;
        $this->userService = $userService;
        $this->enrollmentService = $enrollmentService;
    }

    public function checkCertificateExists($userId, $courseId)
    {
        $certificate = $this->model->where("userId", $userId)->where("courseId", $courseId)->first();
        
        if ($certificate) {
            throw new BadRequestError("User already have this certificate");
        }
    }

    public function generate($userId, $courseId, $courseName)
    {
        $this->checkCertificateExists($userId, $courseId);

        $user = $this->userService->getById($userId);
        $this->enrollmentService->get($userId, $courseId);

        $id = $this->generateId("certificate");

        $defaultTemplate = "blue_mountain.jpg";
        $templatePath = FCPATH . "uploads/certificate_templates/" . $defaultTemplate;

        $templateFile = file_exists($templatePath) ? $defaultTemplate : null;

        $html = view("certificates/template", [
            "user" => $user,
            "courseName" => $courseName,
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
        
        $this->enrollmentService->edit($userId, $courseId);

        $downloadLink = base_url("api/certificates/download/" . $filename);
        $data = [
            "id" => $id,
            "userId" => $userId,
            "courseId" => $courseId,
            "courseName" => $courseName,
            "pdfUrl" => $downloadLink
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

    }

    public function getAll()
    {

    }

    public function delete($id)
    {

    }

    public function verifyCertificateAccess($id)
    {

    }
}