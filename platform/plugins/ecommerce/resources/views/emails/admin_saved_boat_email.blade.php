<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ocean Boats</title>
    <link rel="stylesheet" href="{{ asset('vendor/core/core/base/css/email.css') }}">
</head>

<body style="margin: 0;">
    <table width="100%" id="mainStructure" border="0" cellspacing="0" cellpadding="0"
        style="background-color: #fff;border-spacing: 0;">
        <!-- START TAB TOP -->
        <tbody>
            <!-- ... (rest of your HTML structure) ... -->
            <!-- START MAIN CONTENT-->
            <tr>
                <td align="center" valign="top" class="fix-box" style="border-collapse: collapse;">
                    <!-- start layout-7 container -->
                    <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0"
                        class="full-width" style="border-spacing: 0;">
                        <tbody>
                            <tr>
                                <td valign="top" style="border-collapse: collapse;">
                                    <table width="800" align="center" border="0" cellspacing="0" cellpadding="0"
                                        class="container" bgcolor="#ffffff"
                                        style="background-color: #ffffff;border-spacing: 0;">
                                        <!--start space height -->
                                        <tbody>
                                            <tr>
                                                <td height="30" style="border-collapse: collapse;"></td>
                                            </tr>
                                            <!--end space height -->
                                            <tr>
                                                <td style="min-height: 400px; padding: 15px; font-size: 13px;">

                                                    <h2>A boat has been saved!</h2>

                                                    <p>Hi Admin,</p>

                                                    <a href="{{ url('admin/custom-boat-enquiries') }}" class="button button-blue">View
                                                        Saved Boats</a>
                                                    <br />

                                                    <h3>Boat and Customer Details</h3>

                                                    <table style="width: 100%; border-collapse: collapse;">
                                                        <tr>
                                                            <th
                                                                style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                                                                Boat Title</th>
                                                            <th
                                                                style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                                                                Customer Name</th>
                                                            <th
                                                                style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                                                                Customer Email</th>
                                                            <th
                                                                style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                                                                Customer Phone</th>
                                                            <th
                                                                style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                                                                Total Price</th>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                                                                {{ $boat_title }}</td>
                                                            <td
                                                                style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                                                                {{ $customer_name }}</td>
                                                            <td
                                                                style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                                                                {{ $customer_email }}</td>
                                                            <td
                                                                style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                                                                {{ $customer_phone }}</td>
                                                            <td
                                                                style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                                                                {{ $total_price }}</td>
                                                        </tr>
                                                    </table>
                                                    <br />
                                                </td>
                                            </tr>
                                            <!--start space height -->
                                            <tr>
                                                <td height="28" style="border-collapse: collapse;"></td>
                                            </tr>
                                            <!--end space height -->
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- end layout-7 container -->
                </td>
            </tr>
            <!-- END MAIN CONTENT-->
            <!-- START FOOTER-BOX-->
            <tr>
                <td align="center" valign="top" class="fix-box" style="border-collapse: collapse;">
                    <!-- start layout-7 container -->
                    <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0"
                        class="full-width" style="border-spacing: 0;">
                        <tbody>
                            <tr>
                                <td valign="top" style="border-collapse: collapse;">
                                    <table width="800" align="center" border="0" cellspacing="0" cellpadding="0"
                                        class="full-width" bgcolor="#182955" style="border-spacing: 0;">
                                        <!--start space height -->
                                        <tbody>
                                            <tr>
                                                <td height="20" style="border-collapse: collapse;"></td>
                                            </tr>
                                            <!--end space height -->
                                            <tr>
                                                <td valign="top" align="center" style="border-collapse: collapse;">
                                                    <!-- start logo footer and address -->
                                                    <table width="760" align="center" border="0" cellspacing="0"
                                                        cellpadding="0" class="container" style="border-spacing: 0;">
                                                        <tbody>
                                                            <tr>
                                                                <td valign="top" style="border-collapse: collapse;">
                                                                    <!--start icon navigation -->
                                                                    <table width="100%" border="0" align="center"
                                                                        cellpadding="0" cellspacing="0"
                                                                        style="border-spacing: 0;">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td valign="top" align="center"
                                                                                    style="border-collapse: collapse;">
                                                                                    <table width="100%" border="0"
                                                                                        align="left" cellpadding="0"
                                                                                        cellspacing="0"
                                                                                        class="full-width"
                                                                                        style="border-spacing: 0;">
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td align="left"
                                                                                                    valign="middle"
                                                                                                    class="clear-padding"
                                                                                                    style="border-collapse: collapse;">
                                                                                                    <table
                                                                                                        width="760"
                                                                                                        border="0"
                                                                                                        align="left"
                                                                                                        cellpadding="0"
                                                                                                        cellspacing="0"
                                                                                                        class="col-2"
                                                                                                        style="border-spacing: 0;">
                                                                                                        <tbody>
                                                                                                            <tr>
                                                                                                                <td height="10"
                                                                                                                    style="border-collapse: collapse;">
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                <td
                                                                                                                    style="font-size: 13px;line-height: 15px; text-align: center; font-family: Arial,Tahoma, Helvetica, sans-serif;color: #fff;font-weight: normal;border-collapse: collapse;">
                                                                                                                    ©
                                                                                                                    Ocean
                                                                                                                    Boats
                                                                                                                    2024.
                                                                                                                    All
                                                                                                                    rights
                                                                                                                    reserved.
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <!-- end logo footer and address -->
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <!--start space height -->
                                                            <tr>
                                                                <td height="20" style="border-collapse: collapse;">
                                                                </td>
                                                            </tr>
                                                            <!--end space height -->
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <!-- start space height -->
                                            <tr>
                                                <td height="10" valign="top" style="border-collapse: collapse;">
                                                </td>
                                            </tr>
                                            <!-- end space height -->
                                        </tbody>
                                    </table>
                                    <!-- end layout-FOOTER-BOX container -->
                                </td>
                            </tr>
                            <!-- END FOOTER-BOX-->
                            <!-- START FOOTER COPY RIGHT  -->
                            <tr>
                                <td align="center" valign="top" class="fix-box"
                                    style="border-collapse: collapse;">
                                    <!-- start layout-7 container -->
                                    <table width="100%" align="center" border="0" cellspacing="0"
                                        cellpadding="0" class="full-width" style="border-spacing: 0;">
                                        <!-- start space height -->
                                        <tbody>
                                            <tr>
                                                <td height="5" valign="top" style="border-collapse: collapse;">
                                                </td>
                                            </tr>
                                            <!-- end space height -->
                                            <tr>
                                                <td align="center" valign="top" style="border-collapse: collapse;">
                                                    <table width="800" align="center" border="0"
                                                        cellspacing="0" cellpadding="0" class="container"
                                                        style="border-spacing: 0;">
                                                        <tbody>
                                                            <tr>
                                                                <td valign="top" align="center"
                                                                    style="border-collapse: collapse;">
                                                                    <table width="560" align="center"
                                                                        border="0" cellspacing="0"
                                                                        cellpadding="0" class="container"
                                                                        style="border-spacing: 0;">
                                                                        <tbody>
                                                                            <tr>
                                                                                <!-- start COPY RIGHT content -->
                                                                                <td valign="top" align="center"
                                                                                    style="font-size: 11px;line-height: 22px;font-family: Arial,Tahoma, Helvetica, sans-serif;color: #919191;font-weight: normal;border-collapse: collapse;">
                                                                                    Email is sent from Ocean Boats.
                                                                                </td>
                                                                                <!-- end COPY RIGHT content -->
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <!--  END FOOTER COPY RIGHT -->
                                            <!-- start space height -->
                                            <tr>
                                                <td height="20" valign="top" style="border-collapse: collapse;">
                                                </td>
                                            </tr>
                                            <!-- end space height -->
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
