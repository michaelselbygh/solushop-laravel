<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Delivery Guide</title>
    <style>
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div style="text-align:center">
        <img src="{{ url('portal/images/logo/logo.png') }}" style="height: 30px; width:auto">
        <h4 style="margin: 0px;">Delivery Guide for {{ date('l F j, Y') }}</h4>
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
                    Customer
                </th>
                <th>
                    Signature
                </th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < sizeof($data["deliveries"]); $i++)
                <tr>
                    <td style="padding-bottom: 10px;">
                        {{ $data["deliveries"][$i]["oi_name"] }} <b>( x {{ $data["deliveries"][$i]["oi_quantity"] }} )</b>
                    </td>
                    <td>
                        {{ $data["deliveries"][$i]["order"]["customer"]["first_name"]." ".$data["deliveries"][$i]["order"]["customer"]["last_name"] }}
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
        <h4 style="margin: 0px;">Delivery Guide for {{ date('l F j, Y') }}</h4>
        Customers' Information
    </div>
    <br>

    <table style="width: 100%">
        <thead style="text-align: left;">
            <tr>
                <th style="padding-bottom: 10px;">
                    Customer
                </th>
                <th>
                    Phone
                </th>
                <th>
                    Address
                </th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < sizeof($data["customers"]); $i++)
                <tr>
                    <td style="padding-bottom: 10px;">
                        {{ $data["customers"][$i]->first_name." ".$data["customers"][$i]->last_name }} 
                    </td>
                    <td>
                        {{ "0".substr($data["customers"][$i]->phone, 3) }} 
                    </td>
                    <td style="padding-bottom: 10px;">
                        {{ $data["customers"][$i]->ca_town." - ".$data["customers"][$i]->ca_address }}  
                    </td>
                    
                </tr>
            @endfor
            
        </tbody>
    </table>
</body>
</html>