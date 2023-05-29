

function getLastDateOfMonth(){

    var currentDate = new Date();
    var year = currentDate.getFullYear();
    var month = currentDate.getMonth();
    
    var nextMonthFirstDay = new Date(year, month+1, 1);


    var lastDay = new Date(nextMonthFirstDay.getTime() - 1).getDate();
 
    var formattedDate = year + '-' + String(month+1).padStart(2, '0') + '-' + String(lastDay).padStart(2, '0');

    return formattedDate;

}

function getFirstDateOfMonth(){

    var currentDate = new Date();
    var year = currentDate.getFullYear();
    var month = currentDate.getMonth()+1;
    
    var formattedDate = year + '-' + String(month).padStart(2, '0') + '-' + String(1).padStart(2, '0');

    return formattedDate;

}


function convertabletocsv(tableId,fileName){
    var trArray = $('#'+tableId+' tr').toArray();

    var tableData = '';

    trArray.forEach(function(trElement) {
        var tdArray = $(trElement).find('td,th').toArray();

        tdArray.forEach(function(tdElement) {
            tableData += $(tdElement).text() + ',';
        });

        tableData += '\n';
    });

    exportTableToExcel(tableData, fileName);

}

function exportTableToExcel(tableData, filename) {
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';

    downloadLink = document.createElement('a');
    document.body.appendChild(downloadLink);

    downloadLink.href = 'data:' + dataType + ', ' + encodeURIComponent(tableData);
    downloadLink.download = filename + '.csv';
    downloadLink.click();

    document.body.removeChild(downloadLink);
}