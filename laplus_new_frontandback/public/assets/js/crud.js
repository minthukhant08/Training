/**
 * Created by Dell on 6/24/2016.
 * author Wai Yan Aung
 */

function create_setup(type, user='backend'){
    window.location ='/'+ user +'/' + type + '/create';
}

function edit_setup(type, user='backend') {
    var data = [];
    $("input[name='edit_check']:checked").each(function () {
        data.push($(this).val());
    });
    var d = typeof(data);

    if (data[0] == null) {

        sweetAlert("Oops...", "Please select at least one item to edit !", "error");

    }
    else if (data[1] != null) {

        sweetAlert("Oops...", "Please select only one item to edit !", "error");

    }
    else {
        window.location ='/'+ user +'/' + type + "/edit/" + data;
    }
}

function delete_setup(type, user='backend') {
    var data = [];
    $("input[name='edit_check']:checked").each(function () {
        data.push($(this).val());
    });
    var d = typeof(data);
    if (data[0] == null) {
        sweetAlert("Oops...", "Please select at least one item to delete !", "error");
    }
    //else if (data[1] != null) {
    //    sweetAlert("Oops...", "Please select only one item to delete !", "error");
    //
    //}
    else {
        swal({
                title: "Are you sure?",
                text: "You will not be able to recover!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "Confirm",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {

                    //route path to do deletion in controller

                    $("#selected_checkboxes").val(data);
                    // sweetAlert(data + 'gg', "error");
                    // $("#frm_" + type).submit();
                      window.location = "/"+ user +"/" + type + "/destroy/" + data;

                } else {
                    return;
                }
            });
    }
}

function approve_setup(type) {
    var data = [];
    $("input[name='edit_check']:checked").each(function () {
        data.push($(this).val());
    });
    var d = typeof(data);
    if (data[0] == null) {
        sweetAlert("Oops...", "Please select at least one user to promote !", "error");
    }
    //else if (data[1] != null) {
    //    sweetAlert("Oops...", "Please select only one item to delete !", "error");
    //
    //}
    else {
        swal({
                title: "Are you sure?",
                text: "You will not be able to undo!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "Confirm",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    //window.location = "/" + type + "/destroy/" + data;
                    //route path to do deletion in controller

                    $("#selected_checkboxes").val(data);
                    // $("#frm_" + type).submit();
                    window.location ='/backend/user/approve/' + data;
                } else {
                    return;
                }
            });
    }
}


function cancel_setup(type) {
    window.location.href = '/backend/' + type;
}

$("#check_all").click(function(event){
    if(this.checked) {
        $('.check_source').each(function() { //loop through each checkbox
            this.checked = true;  //select all checkboxes with class "checkbox1"
        });
    }else{
        $('.check_source').each(function() { //loop through each checkbox
            this.checked = false; //deselect all checkboxes with class "checkbox1"
        });
    }
});
