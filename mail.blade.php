<div class="row appline-right  mt-3 mb-3"><div style="width: 100%;padding:20px;">
    		<h4 style="color: #000;"> <b style="color: #ea4f33;" class="text-danger"></b>{{$esubject}}</h4>
    		<table style="width:100%:color:#000">
    				<tbody>
    					<tr>
    						<th style="text-align:left;color: #ea4f33;width:120px;font-size: 13px;">Raised By</th>
    						<td style="font-size: 13px;color: #000;">{{$name}} ({{$email}})</td>
    					</tr>
    					<tr>
    						<th style="text-align:left;color: #ea4f33;width:120px;font-size: 13px;">Subject</th>
    						<td style="font-size: 13px;color: #000;">{{$data}}</td>
    					</tr>
                        <tr>
    						<th style="text-align:left;color: #ea4f33;width:120px;font-size: 13px;">Status</th>
    						<td style="font-size: 13px;color: #000;">{{$status}}</td>
    					</tr>
                        <tr>
                <th style="text-align:left;color: #ea4f33;width:120px;font-size: 13px;">Message</th>
               
                <td style="font-size: 13px;color: #000;">{{$messages}}</td>
           
              </tr>
              
              <tr>
                <th style="text-align:left;color: #ea4f33;width:120px;font-size: 13px;"><a class="btn w-100 btn-warning" href="https://support.ocmsoftware.ie/TicketView/{{$id}}">View Ticket Here</a></th>
              </tr>
              
              

    				</tbody>
    		</table>

</div>
 