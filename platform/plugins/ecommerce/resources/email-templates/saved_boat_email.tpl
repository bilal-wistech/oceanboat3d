{{ header }}

<h2>You have a boat to be booked!</h2>

<p>Hi {{ customer_name }},</p>
<p>We noticed you have booked a boat from our site, would you like to continue?</p>

<a href="{{ site_url }}/customer/saved_boats" class="button button-blue">View Saved Boats</a> or <a href="{{ site_url }}">Go to our Site</a>

<br />

<h3>Boat(s) Details</h3>

<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Boat Title</th>
        <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Total Price</th>
        <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">VAT Total</th>
    </tr>
    <tr>
        <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">{{ boat_title }}</td>
        <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">{{ total_price }}</td>
        <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">{{ vat_total }}</td>
    </tr>
</table>

<br />

<p>If you have any questions, please contact us via <a href="mailto:{{ site_admin_email }}">{{ site_admin_email }}</a></p>

{{ footer }}