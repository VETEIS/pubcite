<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recommendation Letter</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 1in;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .university-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .college-header {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .letter-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .date {
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 20px;
        }
        .signature {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="university-name">University of Southeastern Philippines</div>
        <div class="college-header">{{ $rec_collegeheader }}</div>
        <div class="letter-title">Recommendation Letter</div>
    </div>

    <div class="date">Date: {{ $rec_date }}</div>

    <div class="content">
        <p><strong>To Whom It May Concern,</strong></p>
        
        <p>This is to certify that {{ $rec_facultyname }} is a faculty member of our institution.</p>
        
        <p><strong>Details:</strong> {{ $details }}</p>
        
        <p><strong>Indexing:</strong> {{ $indexing }}</p>
        
        <p>We recommend this faculty member for the requested assistance.</p>
    </div>

    <div class="signature">
        <p>Sincerely,</p>
        <br><br>
        <p><strong>{{ $dean }}</strong><br>
        Dean</p>
    </div>
</body>
</html> 