@extends('layouts.main')
@section('title','Login')
@section('content')


<h3>Commission Data</h3>
<button id="export">Export</button>
<table class="table" id="commTable">
    <thead>
        <tr>
            <th scope="col">S.No</th>
            <th scope="col">Company Name</th>
            <th scope="col">Company Percetange</th>

        </tr>
    </thead>
    <tbody id="tbody">
    </tbody>
</table>

<script>
$(document).ready(function() {
    fetchCommData();

    function fetchCommData() {
        $.ajax({
            url: "{{ route('commissionReport') }}",
            method: 'POST',
            headers: {
                'Accept': 'Application/json',
                'Authorization': 'Bearer 1|gZBqWa3ruSettxpO1aVl1Vi8kJZXPDDG52gq2i4j',
            },
            success: function(res) {
                if (res.statusCode == 'TXN') {
                    if (res.data.length > 0) {
                        html = '';
                        $.each(res.data, function(index, item) {
                            html += `
                                <tr>
                                    <td>` + (index + 1) + `</td>
                                    <td>` + item.company_name + `</td>
                                    <td>` + item.commission_percentage + `</td>
                                </tr>
                            `;

                        });
                        $('#tbody').html(html);
                    }
                } else {
                    $('#tbody').html('');
                }
            }
        });
    }

    $("#export").click(function() {
        convertabletocsv('commTable', 'commReport')
    });


    Echo.channel('ApiCallChannel').listen('ApiHit', (e) => {
        if (e.statusCode == 'TXN') {
            fetchCommData();
        }
    });

})
</script>
@endsection