/*
* tuannt
* 2023-07-05
* */

//Xử lý bắt sự kiện khi blur ra khỏi ô input
$('.input-check-bill').blur(function () {
    let action_url = $('.form-check-bill').attr('action');
    let name_key = $('.input-check-bill').attr('name');
    let bill_code = $('.input-check-bill').val().trim();

    if (!!bill_code) {
        location.replace(action_url + '?' + name_key + '=' + bill_code);
    }
})


// Tra cứu hành trình
function searchKeypress(e) {
    if (e.keyCode == 13) {
        e.preventDefault();

        search_handle($('#txt_search_header').val(), 'BILL');
    }
}

$('.client-check-tracking').on('click', function () {
    search_handle($('#txt_search_header').val(), 'BILL');
})

function search_handle(a, b) {
    // debugger;
    let search_value = a;
    let search_type = b;


    if (search_type == 'BILL') {

        $.ajax({
            url: '/api/client/bill/' + search_value,
            type: 'GET',
            dataType: 'json',
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + $.cookie("api-token")
            },
            success: function(res) {
                console.log('success')
                console.log(res)

                // debugger;
                let html = '';

                if (!!res.bill_info) {
                    html += `
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <i class="fa-regular fa-address-card" title="Partner code"></i>
                                    &nbsp&nbsp
                                ${ res.bill_info.partner_code ? res.bill_info.partner_code : '' }
                                </p>
                            </div>
                            <div class="col-md-6"><p>
                                    <i class="fa-solid fa-location-dot" title="Address"></i>
                                    &nbsp&nbsp
                                ${ res.bill_info.TO_ADD ? res.bill_info.TO_ADD : '' }
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <i class="fa-solid fa-cube" title="Package info"></i>
                                    &nbsp&nbsp
                                ${ res.bill_info.DWS ? res.bill_info.DWS : '' }
                            </p>
                            </div>
                            <div class="col-md-6"><p>
                                <i class="fa-solid fa-barcode" title="Packing list code"></i>
                                    &nbsp&nbsp
                                ${ res.bill_packinglist.packing_list_code ? res.bill_packinglist.packing_list_code : '' }
                                </p>
                            </div>
                        </div>
                        `

                    if (res.bill_journey.length == 0) {

                        html += 'No journey were found';

                    } else {

                        html += `
                                <hr/>
                                <table style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-sm font-weight-bold" style="width: 224px;">DATE</th>
                                            <th class="text-sm font-weight-bold" style="width: ">NOTE</th>
                                            <th class="text-sm font-weight-bold" style="width: ;">STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    `

                        res.bill_journey.forEach(function(v, i) {
                            // Chỉ lấy 5 hành trình gần nhất
                            if (i < 5) {
                                html += (`
                                        <tr>
                                            <td class="text-xs" style="vertical-align: top;">📅 ${v.date_journey}</td>
                                            <td >${v.note} <br><span class="text-sm">-${v.location}</span> </td>

                                            <td class="text-xs font-weight-bold">${  (v.status === null) ? '': v.status}</td>
                                        </tr>
                                `);
                            }
                        })

                        html += `</tbody></table>`

                    };
                } else {
                    html += `
                        <div class="row">
                            <div class="col-md-12" style="text-align:center"><h4>No results were found</h4></div>
                        </div>

                        `;
                }

                $('.btn_print_packing_list').hide();
                $('#showPackinglistModal .modal-body').html(html);
                $('#modalTitle').html('BILL : ' + search_value);
            },
            error: function(err) {
                console.log('err')
                console.log(err)
                let html = '';
                html += `
                        <div class="row">
                            <div class="col-md-12" style="text-align:center"><h4>No results were found</h4></div>
                        </div>

                        `;
                $('#showPackinglistModal .modal-body').html(html);
                $('#modalTitle').html('BILL : ' + search_value);
            }
        });
    } else {
        $.ajax({
            url: '/api/client/packinglist/' + search_value,
            type: 'GET',
            dataType: 'json',
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + $.cookie("api-token")
            },
            success: function(res) {
                let html = '';
                html += `
                            <table style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-sm font-weight-bold" style="width: ;">ORDER PNX</th>
                                        <th class="text-sm font-weight-bold" style="width: ;">ORDER NUMBER</th>
                                        <th class="text-sm font-weight-bold" style="width: ;">TRACKING</th>
                                        <th class="text-sm font-weight-bold" style="width: ;">CREATE AT</th>
                                    </tr>
                                </thead>
                                <tbody>
                            `;

                if (res.length === 0) {
                    html = `
                                    <div class="row">
                                        <div class="col-md-12" style="text-align:center"><h4>No results were found</h4></div>
                                    </div>`;
                } else {
                    res.forEach(function(v, i) {
                        let tt = (v.tracking === null) ? '' : v.tracking;
                        html += (`
                                        <tr>
                                            <td class="text-xs font-weight-bold">${v.order_code}</td>
                                            <td class="text-xs">${v.order_number}</td>
                                            <td class="text-xs">${(v.tracking === null) ? '' : v.tracking} </td>
                                            <td class="text-xs ">${v.created_at}</td>
                                        </tr>
                                    `);
                    })

                    html += `</tbody></table>`

                }

                $('.btn_print_packing_list').show();
                $('#showPackinglistModal .modal-body').html(html);
                $('#modalTitle').html('PACKING LIST : ' + search_value);
            },
            error: function(err) {
                let html = '';
                html += `
                        <div class="row">
                            <div class="col-md-12" style="text-align:center"><h4>No results were found</h4></div>
                        </div>
                        `;
                $('#showPackinglistModal .modal-body').html(html);
                $('#modalTitle').html('PACKING LIST : ' + search_value);
            }
        })

    }

    $("#showPackinglistModal").modal('toggle');
}


// Xử lý in packing list
$('.btn_print_packing_list').on('click', function () {
    PrintElem('#showPackinglistModal .modal-body');
});

function PrintElem(selector)
{
    var divContents = document.querySelector(selector).innerHTML;
    var a = window.open('', '', '');
    a.document.write('<html>');
    a.document.write('<body>');
    a.document.write('<style>');
    a.document.write(`table {
                border-collapse: collapse;
            }
            table td,
            table th {
                border-top: 1px solid #000;
                border-bottom: 1px solid #000;
            }`);
    a.document.write('</style>');
    a.document.write('<h2 style="text-align: center;">PACKING LIST</h2>');
    a.document.write(divContents);
    a.document.write('</body></html>');
    a.document.close();
    a.print();
}
