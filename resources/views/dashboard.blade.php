@extends('layouts.main')
@section('title','Login')
@section('content')

<form action="" id="searchData">
    <div class="row my-4">
        <div class="col col-3">
            <label for="fromDate">Start Date</label>
            <input type="date" name="fromDate" id="fromDate" class="form-control" value="">
        </div>
        <div class="col col-3">
            <label for="toDate">End Date</label>
            <input type="date" name="toDate" id="toDate" class="form-control" value="">
        </div>
        <div class="col col-4">
            <label for="toDate">Status</label>
            <select name="status" class="form-control">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Failed">Failed</option>
                <option value="Success">Success</option>
                <option value="Credit">Credit</option>
            </select>
        </div>
        <div class="col col-2 mt-4">
            <button type="submit" class="btn-success btn">Search</button>
            <button id="export" class="btn-success btn">Export</button>
        </div>
    </div>
</form>
<h3>Recharge Data</h3>
<table class="table" id="summaryTable">
    <thead>
        <tr>
            <th scope="col">S.No</th>
            <th scope="col">Date</th>
            <th scope="col">Company Name</th>
            <th scope="col">Amount</th>
            <th scope="col">Mobile No</th>
            <th scope="col">Status</th>
        </tr>
    </thead>
    <tbody id="tbody">
    </tbody>
</table>

<script>
$(document).ready(function() {
    document.getElementById('fromDate').value = getFirstDateOfMonth();
    document.getElementById('toDate').value = getLastDateOfMonth();
    fetchSummaryData();


    $("#export").click(function() {
        convertabletocsv('summaryTable', 'SummaryReport')
    });


    Echo.channel('ApiCallChannel').listen('ApiHit', (e) => {
        if (e.statusCode == 'TXN') {
            fetchSummaryData();
        }
    });


    function fetchSummaryData(data = '') {
        $.ajax({
            url: "{{ route('summaryData') }}",
            method: 'POST',
            headers: {
                'Accept': 'Application/json',
                'Authorization': 'Bearer 1|gZBqWa3ruSettxpO1aVl1Vi8kJZXPDDG52gq2i4j',
            },
            data: data,
            success: function(res) {
                if (res.statusCode == 'TXN') {
                    if (res.data.length > 0) {
                        html = '';
                        $.each(res.data, function(index, item) {
                            html += `
                                <tr>
                                    <td>` + (index + 1) + `</td>
                                    <td>` + item.date + `</td>                                    
                                    <td>` + item.company + `</td>
                                    <td>` + item.mobile + `</td>
                                    <td>` + item.amount + `</td>                                    
                                    <td>` + item.status + `</td>                                    
                                </tr>
                            `;

                        });
                        $('#tbody').html(html);

                    } else {
                        $('#tbody').html('');

                    }
                }
            }
        });
    }

    $(document).on('submit', '#searchData', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        fetchSummaryData(formData);
    });


})
</script>
@endsection