$(document).ready(function () {

    $("#loginForm").submit(function (event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: 'dbquery/processlogin.php',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response === 'success') {
                    window.location.href = 'dashboard.php';
                } else {
                    alert('Login failed. Please check your credentials.');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            }
        });
    });

    // Clear Form
    $('#ModalForms').click(function () {

        $('#insertForm')[0].reset();
        $('#jobdesc').val('');
        $('#taskdesc').val('');
        $('#subjobdesc').val('');
        $('#notedesc').val('');
        $('#assignname').val('');

        $('#exampleModalLabel').text('Add Ticket');
        $('#btnsaveticket').text('Create');
    });


    var currentPage = 1;
    var defaultStatusID = 1;

    if (window.location.pathname == '/jirattin/ticketlist.php' || window.location.pathname == '/ticketlist.php') {
        buttontickets();
        loadTicketStatus(currentPage, '', defaultStatusID);
    } else if (window.location.pathname == '/jirattin/myassign.php' || window.location.pathname == '/myassign.php') {
        buttonmyassign();
        loadTicketMyassign(currentPage, '');
    } else {
        accomplishentstickets();
        loadTicketDone(0, currentPage);
    }


    // Saving ticket
    $('#btnsaveticket').click(function () {
        var selectedEmployee = $('#assignname').val();
        if (selectedEmployee === '') {
            alert('Please select an employee.');
            return;
        }

        edit_ticket_id = '';

        $(this).prop('disabled', true);

        var data = $('#insertForm').serialize();

        $.ajax({
            url: 'dbquery/processticket.php',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.status === 1) {
                    alert('Ticket updated successfully.');

                    // Reset the form after updating ticket
                    $('#insertForm')[0].reset();

                    $('#customJobField').empty();
                    $('#customSubJobField').empty();
                    populateJobSelect();
                    populateSubJobSelect();
                    loadTicketStatus(1);
                    loadTicketMyassign(1);

                    $('#addTicketModal').modal('hide');
                    $('#btnsaveticket').text('Add');
                    $('#btnsaveticket').prop('disabled', false);

                    $('.statusIDs').load(' .statusIDs', function () {
                        console.log("success");
                        buttontickets();
                        buttonmyassign();
                    });

                    $('#exampleModal').modal('hide');
                } else {
                    alert('Failed to update ticket. Please try again. Error: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                alert('Failed to update ticket. Please try again.');
            }
        });
    });


    function populateJobSelect(department_id, selectedJob) {
        $.ajax({
            url: 'dbquery/joblist.php',
            type: 'GET',
            data: { department_id: department_id },
            dataType: 'json',
            success: function (response) {
                var jobSelect = $('#jobdesc');
                var jobs = response.response;
                jobSelect.empty();

                jobSelect.append($('<option>', {
                    value: '',
                    text: 'Select Job'
                }));

                for (var i = 0; i < jobs.length; i++) {
                    jobSelect.append($('<option>', {
                        value: jobs[i].job_id,
                        text: jobs[i].job,
                        selected: selectedJob == jobs[i].job_id
                    }));
                }

                jobSelect.append($('<option>', {
                    value: '0',
                    text: 'Add New Job'
                }));

                if (selectedJob) {
                    jobSelect.val(selectedJob);
                }

                $('#jobdesc').off('change').on('change', function () {
                    var selectedValue = $(this).val();
                    if (selectedValue == "0") {
                        $(".box-customjob").removeClass("hide");
                    } else {
                        $(".box-customjob").addClass("hide");
                    }
                    $("#customjob").select();
                });
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }


    $('#assignname').change(function () {
        var selectedDepartmentId = $(this).find('option:selected').attr('department_id');
        $("#department_id").val(selectedDepartmentId);
        populateJobSelect(selectedDepartmentId);
        populateSubJobSelect();
    });

    populateJobSelect($('#assignname').find('option:selected').attr('department_id'));
    populateSubJobSelect();


    function populateSubJobSelect() {
        $.ajax({
            url: 'dbquery/subjoblist.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                var subJobSelect = $('#subjobdesc');
                subJobSelect.empty();

                subJobSelect.append($('<option>', {
                    value: '',
                    text: 'Select Sub Job'
                }));

                $.each(response, function (subjob_id, subjob) {
                    subJobSelect.append($('<option>', {
                        value: subjob_id,
                        text: subjob
                    }));
                });

                subJobSelect.append($('<option>', {
                    value: '0',
                    text: 'Add New Subjob'
                }));


                $('#subjobdesc').on('change', function () {
                    var selectedValue = $(this).val();

                    if (selectedValue == "0") {
                        $(".box-customsubjob").removeClass("hide");
                    } else {
                        $(".box-customsubjob").addClass("hide");
                    }

                    $("#customsubjob").select();
                });
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }


    // View, Edit, Delete
    function append_events() {

        // Delete
        $(".deleteTicketBtn").click(function () {
            var delete_ticket_id = $(this).data("ticket-id");
            console.log("Delete Ticket ID:", delete_ticket_id);
            if (confirm("Are you sure you want to delete this ticket?")) {
                $.ajax({
                    url: "dbquery/deleteticket.php",
                    type: "POST",
                    data: { delete_ticket_id: delete_ticket_id },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            alert(response.message);
                            $('#ticketRow_' + delete_ticket_id).remove();
                            loadTicketStatus(0);
                            loadTicketMyassign(0);

                            $('.statusIDs').load(' .statusIDs', function () {
                                console.log("success");

                                buttontickets();
                                buttonmyassign();
                            });
                        } else {
                            alert("Failed to delete ticket. " + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error deleting ticket: ' + error);
                        alert("Failed to delete ticket. Please try again.");
                    }
                });
            }
        });

        // View
        $(".viewTicketBtn").click(function () {
            var ticketId = $(this).data('ticketId');
            showTicketDetails(ticketId);
        });

        $(".pg-ticketlist .close").click(function () {
            $('#ticketModal').modal('hide');
        });

        function showTicketDetails(ticketId) {
            $.ajax({
                url: 'dbquery/viewticket.php',
                type: 'GET',
                data: {
                    ticketId: ticketId
                },
                dataType: 'json',
                success: function (response) {
                    var ticket = response.data[0];

                    var ticketDetails = $('#ticketDetails');
                    ticketDetails.empty();

                    ticketDetails.append('<p>Ticket ID: ' + ticket.ticket_id + '</p>');
                    ticketDetails.append('<p>Job: ' + ticket.job + '</p>');
                    ticketDetails.append('<p>Task: ' + ticket.task + '</p>');
                    ticketDetails.append('<p>Sub Job: ' + ticket.subjob + '</p>');
                    ticketDetails.append('<p>Note: ' + ticket.note + '</p>');
                    ticketDetails.append('<p>Assigned Employee: ' + ticket.assign_nickname + '</p>');
                    ticketDetails.append('<p>Employee Name: ' + ticket.employee_name + '</p>');
                    ticketDetails.append('<p>Date Created: ' + ticket.formatted_date_created + '</p>');
                    ticketDetails.append('<p>Deadline Start: ' + ticket.formatted_deadline_start + '</p>');
                    ticketDetails.append('<p>Deadline End: ' + ticket.formatted_deadline_end + '</p>');
                    ticketDetails.append('<p>Duration Start: ' + ticket.formatted_duration_start + '</p>');
                    ticketDetails.append('<p>Duration End: ' + ticket.formatted_duration_end + '</p>');
                    ticketDetails.append('<p>Ticket Status: ' + ticket.ticketstatus + '</p>');
                    if (ticket.deadline_warning != null) {
                        ticketDetails.append('<p>Deadline Warning: ' + ticket.deadline_warning + '</p>');
                    }

                    $('#ticketModal').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error('Error loading ticket details: ' + error);
                }
            });
        }

        // Edit

        $(".editTicketBtn").click(function () {
            $('#exampleModal').modal('show');
            var edit_ticket_id = $(this).data('ticket-id');
            var department_id = $(this).attr("department_id");

            $.ajax({
                url: 'dbquery/processticket.php',
                type: 'POST',
                data: {
                    edit_ticket_id: edit_ticket_id,
                    department_id: department_id
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 1) {
                        populateJobSelect(department_id, response.job_id);

                        $('#ticket_id').val(edit_ticket_id);
                        $('#jobdesc').val(response.job_id === 0 ? response.customjob : response.job_id);
                        $('#taskdesc').val(response.task);
                        $('#subjobdesc').val(response.subjob_id === 0 ? response.customsubjob : response.subjob_id);
                        $('#notedesc').val(response.note);
                        $('#assignname').val(response.employee_id);
                        $('#deadlinestart').val(response.deadline_start);
                        $('#deadlineend').val(response.deadline_end);
                        $('#durationstart').val(response.duration_start);
                        $('#durationend').val(response.duration_end);
                        $('#ticketstatus').val(response.ticketstatus_id);
                        $('#exampleModalLabel').text('Edit Ticket');
                        $('#btnsaveticket').text('Update');

                    } else {
                        alert('Failed to retrieve ticket details. Please try again.');
                    }
                },
                error: function (e) {
                    console.error('Error fetching ticket details:', e);
                    alert('Failed to retrieve ticket details. Please try again.');
                }
            });
        });

    }


    // Create user
    $('#btnCreateuser').click(function () {

        var data = $('#createForm').serialize();

        $.ajax({
            url: 'dbquery/processuser.php',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    alert('User added successfully.');

                    $('#createForm')[0].reset();
                } else {
                    alert('Failed to add user. Please try again.');
                    $('#btnCreateuser').prop('disabled', false);
                }
            }
        });
    });

    // View ticket button

    // Ticket list

    function buttontickets() {
        $('.btnsearch').click(function () {
            var searchQuery = $('#searchInput').val();
            var currentPage = 1;
            loadTicketStatus(currentPage, searchQuery);
        });

        $('.btnstatus').click(function () {
            $('.btnstatus').removeClass('active');
            $(this).addClass('active');
            var currentPage = 1;
            var searchQuery = $('#searchInput').val();
            loadTicketStatus(currentPage, searchQuery, $(this).data('status'));
        });

        $('.btn-clear-search').click(function () {
            clearSearchInput();
            $('.btnstatus').removeClass('active');
            $('.btnstatus[data-status="' + defaultStatusID + '"]').addClass('active');
            loadTicketStatus(currentPage, '');
        });
    }

    function append_pageticketlist() {
        $(".pagination a").click(function (e) {
            e.preventDefault();
            var currentPage = $(this).attr('href').split('Activepage=')[1];
            var searchQuery = $('#searchInput').val();
            loadTicketStatus(currentPage, searchQuery, $('.btnstatus.active').data('status'));
        });
    }

    function clearSearchInput() {
        console.log("Clearing search input...");
        document.getElementById('searchInput').value = '';
        var currentPage = 1;
        loadTicketStatus(currentPage, '', defaultStatusID);
    }

    function loadTicketStatus(pageNumber, searchQuery = '', statusID = defaultStatusID) {
        $.ajax({
            url: 'dbquery/viewticket.php',
            type: 'GET',
            data: {
                searchQuery: searchQuery,
                Activepage: pageNumber,
                statusID: statusID
            },
            dataType: 'json',
            success: function (response) {
                var tableBody = $('#ticketdata');
                tableBody.empty();

                $.each(response.data, function (index, ticket) {
                    var row = $('<tr>');
                    row.append('<td>' + (ticket.ticket_id ? ticket.ticket_id : '') + '</td>');
                    row.append('<td>' + 'Job: ' + '' + (ticket.job ? ticket.job : '') + '<br>' + 'Task: ' + '' + (ticket.task ? ticket.task : '') + (ticket.subjob ? '<br>' + 'Subjob: ' + ticket.subjob : '') + (ticket.note ? '<br>' + 'Note: ' + ticket.note : '') + (ticket.formatted_date_accepted ? '<br>' + 'Accepted Time: ' + ticket.formatted_date_accepted : '') + '</td>');
                    row.append('<td>' + 'Created By: ' + '' + (ticket.assign_nickname ? ticket.assign_nickname : '') + '<br>' + 'Assign To: ' + '' + (ticket.employee_name ? ticket.employee_name : '') + '</td>');
                    row.append('<td>' + 'Start: ' + '' + (ticket.formatted_deadline_start ? ticket.formatted_deadline_start : '') + '<br>' + 'End: ' + '' + (ticket.formatted_deadline_end ? ticket.formatted_deadline_end : '') + '</td>');
                    row.append('<td>' + 'Start: ' + '' + (ticket.formatted_duration_start ? ticket.formatted_duration_start : '') + '<br>' + 'End: ' + '' + (ticket.formatted_duration_end ? ticket.formatted_duration_end : '') + '</td>');

                    var statusCell = $('<td>').text(ticket.ticketstatus ? ticket.ticketstatus : '');
                    if (ticket.deadline_warning) {
                        statusCell.append($('<br>' + '<span>').text(ticket.deadline_warning).css('color', 'red'));
                    }
                    row.append(statusCell);

                    if (ticket.ticketstatus !== 'Done' && ticket.ticketstatus !== 'Abandoned' && ticket.ticketstatus !== 'Declined') {
                        var actionButtons = '<td>' +
                            // '<button class="btn btn-info btn-sm viewTicketBtn" data-ticket-id="' + ticket.ticket_id + '"><i class="bi bi-eye"></i></button>' +
                            '<button class="btn btn-warning btn-sm editTicketBtn" department_id = "' + ticket.department_id + '" data-ticket-id="' + ticket.ticket_id + '"><i class="bi bi-pencil-square"></i></button>' +
                            '<button class="btn btn-danger btn-sm deleteTicketBtn" data-ticket-id="' + ticket.ticket_id + '"><i class="bi bi-trash"></i></button>' +
                            '</td>';
                        row.append(actionButtons);
                    } else {
                        row.append('<td></td>');
                    }

                    tableBody.append(row);
                });


                $('#pagination').html(response.pagination);

                append_events();
                append_pageticketlist();
            },

            error: function (xhr, status, error) {
                console.error('Error loading ticket data: ' + error);
            }
        });
    }

    // My Assign
    function buttonmyassign() {
        $('.btnsearchh').click(function () {
            var searchQuery = $('#searchInputs').val();
            var status = $('.btnstatuss.active').data('status');
            var currentPage = 1;
            loadTicketMyassign(status, currentPage, searchQuery);
        });

        $('.btnstatuss').click(function () {
            $('.btnstatuss').removeClass('active');
            $(this).addClass('active');
            var currentPage = 1;
            var searchQuery = $('#searchInputs').val();
            var status = $(this).data('status');
            loadTicketMyassign(status, currentPage, searchQuery);
        });

        $('.btn-clear-searchh').click(function () {
            clearSearchInputs();
            $('.btnstatuss').removeClass('active');
        });
    }

    function append_pagemyassign() {
        $(".paginations a").click(function (e) {
            e.preventDefault();
            var currentPage = $(this).attr('href').split('Activepage=')[1];
            var searchQuery = $('#searchInputs').val();
            var status = $('.btnstatuss.active').data('status');
            loadTicketMyassign(status, currentPage, searchQuery);
        });
    }

    function clearSearchInputs() {
        console.log("Clearing search input...");
        document.getElementById('searchInputs').value = '';
        var status = 0;
        var currentPage = 1;
        loadTicketMyassign(status, currentPage);
    }

    function loadTicketMyassign(status, pageNumber, searchQuery = '') {

        $.ajax({
            url: 'dbquery/viewticketmyassign.php',
            type: 'GET',
            data: {
                statusID: status,
                searchQuery: searchQuery,
                Activepage: pageNumber
            },
            dataType: 'json',
            success: function (response) {
                var tableBody = $('#ticketdataa');
                tableBody.empty();

                $.each(response.data, function (index, ticket) {
                    var row = $('<tr>');
                    row.append('<td>' + (ticket.ticket_id ? ticket.ticket_id : '') + '</td>');
                    row.append('<td>' + 'Job: ' + '' + (ticket.job ? ticket.job : '') + '<br>' + 'Task: ' + '' + (ticket.task ? ticket.task : '') + (ticket.subjob ? '<br>' + 'Subjob: ' + ticket.subjob : '') + (ticket.note ? '<br>' + 'Note: ' + ticket.note : '') + (ticket.formatted_date_accepted ? '<br>' + 'Accepted Time: ' + ticket.formatted_date_accepted : '') + '</td>');
                    row.append('<td>' + 'Created By: ' + '' + (ticket.assign_nickname ? ticket.assign_nickname : '') + '<br>' + 'Assign To: ' + '' + (ticket.employee_name ? ticket.employee_name : '') + '</td>');
                    row.append('<td>' + 'Start: ' + '' + (ticket.formatted_deadline_start ? ticket.formatted_deadline_start : '') + '<br>' + 'End: ' + '' + (ticket.formatted_deadline_end ? ticket.formatted_deadline_end : '') + '</td>');
                    row.append('<td>' + 'Start: ' + '' + (ticket.formatted_duration_start ? ticket.formatted_duration_start : '') + '<br>' + 'End: ' + '' + (ticket.formatted_duration_end ? ticket.formatted_duration_end : '') + '</td>');

                    var statusCell = $('<td>').text(ticket.ticketstatus ? ticket.ticketstatus : '');
                    if (ticket.deadline_warning) {
                        statusCell.append($('<br>' + '<span>').text(ticket.deadline_warning).css('color', 'red'));
                    }
                    row.append(statusCell);

                    if (ticket.ticketstatus !== 'Done' && ticket.ticketstatus !== 'Abandoned' && ticket.ticketstatus !== 'Declined') {
                        var actionButtons = '<td>' +
                            // '<button class="btn btn-info btn-sm viewTicketBtn" data-ticket-id="' + ticket.ticket_id + '"><i class="bi bi-eye"></i></button>' +
                            '<button class="btn btn-warning btn-sm editTicketBtn" department_id = "' + ticket.department_id + '" data-ticket-id="' + ticket.ticket_id + '"><i class="bi bi-pencil-square"></i></button>' +
                            '<button class="btn btn-danger btn-sm deleteTicketBtn" data-ticket-id="' + ticket.ticket_id + '"><i class="bi bi-trash"></i></button>' +
                            '</td>';
                        row.append(actionButtons);
                    } else {
                        row.append('<td></td>');
                    }

                    tableBody.append(row);
                });

                $('#paginations').html(response.pagination);

                append_events();
                append_pagemyassign();
            },

            error: function (xhr, status, error) {
                console.error('Error loading ticket data: ' + error);
            }
        });
    }

    // Accomplishments

    function accomplishentstickets() {
        $("#btnFilterEmployee").click(function () {
            var selectedEmployee = $('#assignnametime').val();
            var status = $('.btnstatus.active').data('status');
            var currentPage = 1;
            loadTicketDone(status, currentPage, selectedEmployee);
        });

        $(".btn-clear-searchdone").click(function () {
            clearSearchInputDone();
        });

        $(".startclick").click(function () {
            filterDate();
        });

        $(".resetclick").click(function () {
            resetDate();
        });

        $(".startdayclick").click(function () {
            filterDay();
        });

        $(".resetdayclick").click(function () {
            resetDay();
        });

        $("#btnResetEmployee").click(function () {
            resetEmp();
        });
    }

    function append_pageaccomplishment() {
        $(".paginationdone a").click(function (e) {
            e.preventDefault();
            var currentPage = $(this).attr('href').split('Activepage=')[1];
            var searchQuery = $('#assignnametime').val();
            var status = $('.btnstatus.active').data('status');
            loadTicketDone(status, currentPage, searchQuery);
        });
    }

    function resetEmp() {
        $('#assignnametime').val('');

        var status = $('.btnstatus.active').data('status');
        var currentPage = 1;
        loadTicketDone(status, currentPage);
    }

    function filterDay() {
        var selectedDate = $('#datePicker').val();
        var status = $('.btnstatus.active').data('status');
        var currentPage = 1;
        var searchQuery = $('#assignnametime').val();
        var startDate = '';
        var endDate = '';
        loadTicketDone(status, currentPage, searchQuery, startDate, endDate, selectedDate);
    }

    function resetDay() {
        console.log("Clearing selected date...");
        $('#datePicker').val('');

        var status = $('.btnstatus.active').data('status');
        var currentPage = 1;
        var searchQuery = $('#assignnametime').val();
        var startDate = '';
        var endDate = '';
        var selectedDate = '';

        loadTicketDone(status, currentPage, searchQuery, startDate, endDate, selectedDate);
    }

    function filterDate() {
        var startDate = $('#startdate').val();
        var endDate = $('#enddate').val();
        var currentPage = 1;
        var searchQuery = $('#assignnametime').val();
        var status = $('.btnstatus.active').data('status');
        loadTicketDone(status, currentPage, searchQuery, startDate, endDate);
    }

    function resetDate() {
        console.log("Clearing search input...");
        $('#startdate').val('');
        $('#enddate').val('');
        var status = 0;
        var currentPage = 1;
        loadTicketDone(status, currentPage);
    }

    function clearSearchInputDone() {
        console.log("Clearing search input...");
        $('#assignnametime').val('');
        var status = 0;
        var currentPage = 1;
        loadTicketDone(status, currentPage);
    }

    function loadTicketDone(status, pageNumber, searchQuery = '', startDate = '', endDate = '', selectedDate = '') {
        var selectedEmployee = $('#assignnametime').val();

        $.ajax({
            url: 'dbquery/accomplishmentsticket.php',
            type: 'GET',
            data: {
                statusID: status,
                Activepage: pageNumber,
                searchQuery: searchQuery,
                employee_id: selectedEmployee,
                startdate: startDate,
                enddate: endDate,
                selectedDate: selectedDate,
            },
            dataType: 'json',
            success: function (response) {
                var ticketData = response.data;

                var ticketsByDate = {};
                ticketData.forEach(function (ticket) {
                    var date = ticket.formatted_duration_end.split(' ');
                    var month = date[0];
                    var day = date[1];
                    var year = date[2];

                    var dateKey = month + ' ' + day + ', ' + year;

                    if (!ticketsByDate[dateKey]) {
                        ticketsByDate[dateKey] = [];
                    }
                    ticketsByDate[dateKey].push(ticket);
                });

                var tableBody = $('#ticketdone');
                tableBody.empty();

                Object.keys(ticketsByDate).forEach(function (date) {
                    tableBody.append('<tr><td colspan="6" style="width: 100%;">Date: ' + date + '</td></tr>');
                    ticketsByDate[date].forEach(function (ticket) {
                        var row = $('<tr>');
                        // row.append('<td>' + (ticket.formatted_date_created ? ticket.formatted_date_created : '') + '</td>');
                        row.append('<td></td>');
                        row.append('<td>' + 'Job: ' + '' + (ticket.job ? ticket.job : '') + '<br>' + 'Task: ' + '' + (ticket.task ? ticket.task : '') + (ticket.subjob ? '<br>' + 'Subjob: ' + ticket.subjob : '') + '</td>');
                        row.append('<td>' + (ticket.employee_name ? ticket.employee_name : '') + '</td>');
                        row.append('<td>' + (ticket.ticketstatus ? ticket.ticketstatus : '') + '</td>');
                        tableBody.append(row);
                    });
                });

                $('#paginationdone').html(response.pagination);

                append_pageaccomplishment();
            },
            error: function (xhr, status, error) {
                console.error('Error loading ticket data: ' + error);
            }
        });
    }


});


