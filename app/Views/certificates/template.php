<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Certificate</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: "Helvetica", sans-serif;
            background-image: <?= isset($templateFile) ? "url('" . base_url('uploads/certificate_templates/' . $templateFile) . "')" : "none" ?>;
            background-size: cover;
            background-position: center;
        }

        .content {
            background-color: rgba(255, 255, 255, 0.85);
            width: 100%;
            max-width: 576px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            padding: 0 40px;
            border-radius: 10px;
        }

        .title {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .name { font-size: 36px; font-weight: bold; margin: 20px 0; }
        .courseName { font-size: 28px; margin-bottom: 36px; }
        .certificateId, .date {
            font-size: 20px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="title">Certificate of Completion</div>
        <p>This certifies that</p>
        <div class="name"><?= esc($user->name) ?></div>
        <p>has successfully completed the course</p>
        <div class="courseName"><?= esc($courseName) ?></div>
        <div class="certificateId">Certificate ID: <?= esc($certificateId) ?></div>
        <div class="date"><?= esc($date) ?></div>
    </div>
</body>
</html>
