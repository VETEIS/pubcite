<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Incentive Application Form</title>
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
        .form-title {
            font-size: 12pt;
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
        .signature-line {
            border-bottom: 1px solid black;
            display: inline-block;
            width: 200px;
            margin: 0 10px;
        }
        .date-line {
            border-bottom: 1px solid black;
            display: inline-block;
            width: 100px;
            margin: 0 10px;
        }
        .signature-section {
            margin-top: 20px;
        }
        .signature-row {
            margin-bottom: 15px;
        }
        .signature-name {
            margin-top: 5px;
        }
        .checkbox {
            font-family: 'Wingdings', serif;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="university-name">University of Southeastern Philippines</div>
        <div class="form-title">Application Form for Research Publication Incentive</div>
    </div>

    <div class="section-title">I. Personal Profile</div>
    <div class="field">Name of the Applicant: {{ $name }}</div>
    <div class="field">Academic Rank: {{ $academicrank }} Employment Status: {{ $employmentstatus }}</div>
    <div class="field">College: {{ $college }} Campus: {{ $campus }}</div>
    <div class="field">Field of Specialization: {{ $field }} No. of Years in the University: {{ $years }}</div>

    <div class="section-title">II. Details of the Published paper:</div>
    <div class="field">Title of the published paper: {{ $papertitle }}</div>
    <div class="field">Co-authors (if any): {{ $coauthors }}</div>
    <div class="field">Title of the Journal: {{ $journaltitle }}</div>
    <div class="field">Vol./Issue No./Year: {{ $version }} P-ISSN: {{ $pissn }} E-ISSN: {{ $eissn }}</div>
    <div class="field">DOI (for e-journal): {{ $doi }}</div>
    <div class="field">Publisher: {{ $publisher }}</div>
    <div class="field">Type of Publication (Tick one): {{ $regional }} Regional   {{ $national }} National   {{ $international }} International</div>
    <div class="field">Indexed in: {{ $scopus }}Scopus     {{ $wos }} Web of Science     {{ $aci }}ACI     {{ $pubmed }}PubMed</div>
    <div class="field">Scopus CiteScore (if applicable): {{ $citescore }}</div>

    <div class="section-title">III. Details of the assistance to be requested from the University</div>
    <div class="field">(Please write N/A if not applicable.)</div>
    <div class="field">Particulars / Amount: {{ $particulars }}</div>

    <div class="section-title">Please submit/attach the following documents:</div>
    <div class="field">a. Copy of the published article (PDF copy)</div>
    <div class="field">b. Copy of the Cover and Table of Contents of the journal issue (PDF copy)</div>
    <div class="field">c. Certificate/Letter of Acceptance or any similar documents for the publication (PDF copy)</div>
    <div class="field">d. Peer review reports, manuscript drafts with tracked changes and reviewers' comments, or similar proof of peer review reports prior to publication</div>
    <div class="field">e. Terminal Report</div>

    <div class="section-title">IV. Declaration</div>
    <div class="field">I hereby declare that all the details in this application form are accurate. I have not hidden any relevant information as must necessarily brought to the attention of the University. I will satisfy all the terms and conditions prescribed in the guidelines of the University for research paper publication.</div>

    <div class="signature-section">
        <div class="field">Signed by:</div>
        <div class="signature-row">
            <span class="signature-line"></span><span class="date-line"></span>
        </div>
        <div class="field">   Signature over Printed Name of the Faculty				       Date</div>
        <div class="signature-name">{{ $facultyname }}</div>

        <div class="field">Noted by:</div>
        <div class="signature-row">
            <span class="signature-line"></span><span class="date-line"></span>
        </div>
        <div class="field">                  Research Center Manager				     	       Date</div>
        <div class="signature-name">{{ $centermanager }}</div>

        <div class="signature-row">
            <span class="signature-line"></span><span class="date-line"></span>
        </div>
        <div class="field">                             College Dean			 		        	       Date</div>
        <div class="signature-name">{{ $collegedean }}</div>

        <div class="field">Approved by:</div>
        <div class="signature-row">
            <span class="signature-line"></span><span class="date-line"></span>
        </div>
        <div class="field">Deputy Director, Publication Unit	           		  	       Date</div>

        <div class="signature-row">
            <span class="signature-line"></span><span class="date-line"></span>
        </div>
        <div class="field">   Director, Research and Development Division			       Date</div>
    </div>
</body>
</html> 