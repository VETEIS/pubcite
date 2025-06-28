<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Terminal Report</title>
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
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .field {
            margin-bottom: 5px;
        }
        .content {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="university-name">University of Southeastern Philippines</div>
        <div class="report-title">Terminal Report</div>
    </div>

    <div class="field"><strong>Title:</strong> {{ $title }}</div>
    <div class="field"><strong>Author:</strong> {{ $author }}</div>
    <div class="field"><strong>Duration:</strong> {{ $duration }}</div>

    <div class="section-title">Abstract</div>
    <div class="content">{{ $abstract }}</div>

    <div class="section-title">Introduction</div>
    <div class="content">{{ $introduction }}</div>

    <div class="section-title">Methodology</div>
    <div class="content">{{ $methodology }}</div>

    <div class="section-title">Results and Discussion</div>
    <div class="content">{{ $rnd }}</div>

    <div class="section-title">Conclusion and Recommendations</div>
    <div class="content">{{ $car }}</div>

    <div class="section-title">References</div>
    <div class="content">{{ $references }}</div>

    <div class="section-title">Appendices</div>
    <div class="content">{{ $appendices }}</div>
</body>
</html> 