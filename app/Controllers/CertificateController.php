<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Services;

class CertificateController extends BaseController
{
   protected $service;

   public function __construct()
   {
        $this->service = service("certificateService");
   }

   public function store()
   {
        $certificateData = validateRequest("certificate");
        $userId = Services::userContext()->getUserId();

        $result = $this->service->generate($userId, $certificateData["courseId"], $certificateData["courseName"]);

        $downloadLink = $result["downloadLink"];
        $certificateId = $result["id"];

        return $this->respond([
            "status" => "success",
            "message" => "certificate created",
            "data" => [
                "certificate" => [
                    "id" => $certificateId
                ],
                "downloadUrl" => $downloadLink,
            ]
        ], 201);
   }

   public function download($filename)
   {
       $path = WRITEPATH . "certificates/" . $filename;

        if (!file_exists($path)) {
            return $this->response->setStatusCode(404)->setJSON([
                "status" => "fail",
                "message" => "Certificate not found"
            ]);            
        }

        return $this->response->download($path, null)->setFileName($filename);
   }

   public function upload()
   {
        $file = $this->request->getFile("template");

        $filename = $this->service->uploadTemplate($file);

        return $this->respond([
            "status" => "success",
            "filename" => $filename
        ], 201);
   }

   public function test()
   {
        $defaultTemplate = "blue_mountain.jpg";
        $templatePath = FCPATH . "uploads/certificate_templates/" . $defaultTemplate;

        $templateFile = file_exists($templatePath) ? $defaultTemplate : null;

        return view("certificates/template", [
            "user" => (object) array("name" => "Axel"),
            "courseName" => "Belajar Meltryllis",
            'date' => date('F j, Y'),
            "certificateId" => "course-1234",
            "templateFile" => $templateFile
        ]);
   }
}
