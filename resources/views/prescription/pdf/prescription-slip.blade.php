<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title>Axiomed Prescription Slip</title>

	<!-- Bootstrap cdn 3.3.7 -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Custom font montseraat -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700" rel="stylesheet">

	<!-- Custom style -->

    <style>
        .back{}
        .invoice-wrapper{
            margin: 20px auto;
            max-width: 700px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
        }
        .invoice-top{
            background-color: #fafafa;
            padding: 5px 40px;
        }
        /*
        Invoice-top-left refers to the client name & address, service provided
        */
        .invoice-top-left{
            margin-top: 60px;
        }
        .invoice-top-left h2 , .invoice-top-left h6{
            line-height: 1.5;
            font-family: 'Montserrat', sans-serif;
        }
        .invoice-top-left h4{
            margin-top: 30px;
            font-family: 'Montserrat', sans-serif;
        }
        .invoice-top-left h5{
            line-height: 1.4;
            font-family: 'Montserrat', sans-serif;
            font-weight: 400;
        }
        .client-company-name{
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 0;
        }
        .client-address{
            font-size: 14px;
            margin-top: 5px;
            color: rgba(0,0,0,0.75);
        }

        /*
        Invoice-top-right refers to the our name & address, logo and date
        */
        .invoice-top-right h2 , .invoice-top-right h6{
            text-align: right;
            line-height: 1.5;
            font-family: 'Montserrat', sans-serif;
        }
        .invoice-top-right h5{
            line-height: 1.4;
            font-family: 'Montserrat', sans-serif;
            font-weight: 400;
            text-align: right;
            margin-top: 0;
        }
        .our-company-name{
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 0;
        }
        .our-address{
            font-size: 13px;
            margin-top: 0;
            color: rgba(0,0,0,0.75);
        }
        .logo-wrapper{ overflow: auto; }

        /*
        Invoice-bottom refers to the bottom part of invoice template
        */
        .invoice-bottom{
            background-color: #ffffff;
            padding: 40px 60px;
            position: relative;
        }
        .title{
            font-size: 30px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            margin-bottom: 30px;
        }
        /*
        Invoice-bottom-left
        */
        .invoice-bottom-left h5, .invoice-bottom-left h4{
            font-family: 'Montserrat', sans-serif;
        }
        .invoice-bottom-left h4{
            font-weight: 400;
        }
        .terms{
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            margin-top: 40px;
        }
        .divider{
            margin-top: 50px;
            margin-bottom: 5px;
        }
        /*
        bottom-bar is colored bar located at bottom of invoice-card
        */
        .bottom-bar{
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 26px;
            background-color: #323149;
        }
        .btn-primary-download {
            padding: 10px;            
            background: rgb(241, 129, 0);
            color: #fff;
            position: absolute;
            top: 10px;
            right: 10px;
            text-decoration: none;
            border-radius: 10px;
        }
    </style>
    <style>
        /* Hide headers and footers when printing */
		@media print {
		@page {
			size: auto;
			margin: 0;
		}

		body {
			margin: 0;
            font-size: 14px;
		}

		/* Hide header and footer elements */
		header, footer {
			display: none;
		}
        .noprint {
            visibility: hidden;
        }
		}
    </style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
    
    function downloadResume() {
      // Print the specific div content
      // var contentToPrint = document.getElementById('print').innerHTML;
      // document.body.innerHTML = contentToPrint;

      const contentDiv = document.getElementById('print');
      console.log(contentDiv);
      const opt = {
        margin: 0,
        filename: 'prescription-slip-{{ $prescription->id }}.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { scale: 4, dpi:192 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
      };

      html2pdf().from(contentDiv).set(opt).save();

      // Trigger print
    //   window.print();

      // Delay the redirection for 2 seconds (2000 milliseconds)
     
    }
</script>
</head>
<body>
    <section class="back">
        <a href="#" onclick="downloadResume()" class="btn-primary-download noprint">Download</a>
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="invoice-wrapper">
                        <div id="print">
                            <div class="invoice-top">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="invoice-top-left">
                                            {{-- <h2 class="client-company-name">Recommend By</h2> --}}
                                            {{-- <h6 class="client-address">Dr John</h6> --}}
                                            <h4>Prescription Info</h4>
                                            <h5>No.: {{ $prescription->prescription_no }}</h5>
                                            <h5>Date: {{ date('d F Y', strtotime($prescription->created_at)) }}</h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="invoice-top-right">
                                            <h2 class="our-company-name">Axiomeds LLP</h2>
                                            <h6 class="our-address">477 Blackwell Street, <br>Dry Creek, Alaska <br>USA</h6>
                                            <div class="logo-wrapper">
                                                <img src="https://images.squarespace-cdn.com/content/v1/5d079400cba6190001008b45/1561651013320-RLOBAJ8AZ23VZNJIPX2K/AXIO_Logo_tagline_2019.png?format=1500w" class="img-responsive pull-right logo" />
                                            </div>
                                            {{-- <h5>{{ date('d F Y', strtotime($prescription->created_at)) }}</h5> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-bottom">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body p-0">
                                                <div class="row pb-5 p-5">
                                                    <div class="col-md-6">
                                                        <p class="font-weight-bold mb-4">Patient Information</p>
                                                        <p class="mb-1">Name: {{ $prescription->appointment->patient->first_name ?? '' }} {{ $prescription->appointment->patient->last_name ?? '' }}</p>
                                                        <p>Age: {{ $prescription->appointment->patient->age ?? '' }}</p>
                                                    </div>
                            
                                                    <div class="col-md-6 text-right">
                                                        {{-- <p class="font-weight-bold mb-4">Doctor Information</p> --}}
                                                        {{-- <p class="mb-1"><span class="text-muted">Prescription No.: </span> John Doe</p>
                                                        <hr>                                                       --}}
                                                        <p class="font-weight-bold mb-4">Doctor Information</p>
                                                        <p class="mb-1"><span class="text-muted">Name: </span> {{ $prescription->doctor->first_name ?? '' }} {{ $prescription->doctor->last_name ?? '' }}</p>
                                                        <p class="mb-1"><span class="text-muted">Contact: </span> {{ $prescription->doctor->mobile ?? '' }}</p>
                                                        <p class="mb-1"><span class="text-muted">Email </span> {{ $prescription->doctor->user->email ?? '' }}</p>                                                        
                                                    </div>
                                                </div>
                            
                                                <div class="row p-5">
                                                    <div class="col-md-12">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="border-0 text-uppercase small font-weight-bold">Medication Name</th>
                                                                    <th class="border-0 text-uppercase small font-weight-bold">Sig</th>
                                                                    <th class="border-0 text-uppercase small font-weight-bold">Qty</th>
                                                                    <th class="border-0 text-uppercase small font-weight-bold">Unit</th>
                                                                    <th class="border-0 text-uppercase small font-weight-bold">Diagnosis</th>
                                                                    <th class="border-0 text-uppercase small font-weight-bold">Interaction</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($prescription->prescriptionData as $item)                                                                 
                                                                    <tr>
                                                                        <td>{{ $item->medication_name }}</td>
                                                                        <td>{{ $item->sig }}</td>
                                                                        <td>{{ $item->qty }}</td>
                                                                        <td>{{ $item->unit }}</td>
                                                                        <td>{{ $item->diagnosis }}</td>
                                                                        <td>{{ $item->interaction }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            {{-- <tbody>
                                                                <tr>
                                                                    <td>Pharmacy Instruction:</td>
                                                                    <td>Lorem Ipsum</td>
                                                                </tr>
                                                            </tbody> --}}
                                                        </table>
                                                    </div>
                                                </div>                                        
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bottom-bar">
                                    <div class="col-sm-4" style="color: #fff">
                                        <h6 class="text-left">axiomeds.com</h6>
                                    </div>
                                    <div class="col-sm-4">
                                        <h6 class="text-center" style="color: #fff">contact@axiomeds.com</h6>
                                    </div>
                                    <div class="col-sm-4">
                                        <h6 class="text-right" style="color: #fff">+1 123123123</h6>
                                    </div>

                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</section>
	

	<!-- jquery slim version 3.2.1 minified -->
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha256-k2WSCIexGzOj3Euiig+TlR8gA0EmPjuc79OEeY5L45g=" crossorigin="anonymous"></script>

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

</body>
</html>