<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pick Up Guide</title>
    <style>
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div style="text-align:center">
        <img src="{{ url('portal/images/logo/logo.png') }}" style="height: 30px; width:auto">
        <h4 style="margin: 0px;">Pick Up Guide for {{ date('l F j, Y') }}</h4>
        Products' Information
    </div>
    <br>

    <table style="width: 100%">
        <thead style="text-align: left;">
            <tr>
                <th style="padding-bottom: 10px;">
                    Product
                </th>
                <th>
                    Vendor
                </th>
                <th>
                    Signature
                </th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < sizeof($data["pick_ups"]); $i++)
                <tr>
                    <td style="padding-bottom: 10px;">
                        {{ $data["pick_ups"][$i]["oi_name"] }} <b>( x {{ $data["pick_ups"][$i]["oi_quantity"] }} )</b>
                    </td>
                    <td>
                        {{ $data["pick_ups"][$i]["sku"]["product"]["vendor"]["name"] }}
                    </td>
                    <td>
                        ..........................
                    </td>
                </tr>
            @endfor
            
        </tbody>
    </table>

    <div class="page-break"></div>
    <div style="text-align:center">
        <img src="{{ url('portal/images/logo/logo.png') }}" style="height: 30px; width:auto">
        <h4 style="margin: 0px;">Pick Up Guide for {{ date('l F j, Y') }}</h4>
        Vendors' Information
    </div>
    <br>

    <table style="width: 100%">
        <thead style="text-align: left;">
            <tr>
                <th style="padding-bottom: 10px;">
                    Vendor
                </th>
                <th>
                    Main Phone
                </th>
                <th>
                    Alt. Phone
                </th>
                <th>
                    Pick Up Address
                </th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < sizeof($data["vendors"]); $i++)
                <tr>
                    <td style="padding-bottom: 10px;">
                        {{ $data["vendors"][$i]->name }} 
                    </td>
                    <td>
                        {{ "0".substr($data["vendors"][$i]->phone, 3) }} 
                    </td>
                    <td style="padding-bottom: 10px;">
                        {{ "0".substr($data["vendors"][$i]->alt_phone, 3) }} 
                    </td>
                    <td>
                        {{ $data["vendors"][$i]->address }} 
                    </td>
                    
                </tr>
            @endfor
            
        </tbody>
    </table>
</body>
</html>