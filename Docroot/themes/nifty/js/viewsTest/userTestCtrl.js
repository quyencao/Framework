$(function () {
    $('#form-edit').bootstrapValidator({
        excluded: [':disabled'],
        fields: {
            fullName: {
                validators: {
                    notEmpty: {
                        message: 'Vui lòng nhập tên đầy đủ'
                    }
                }
            },
            jobTitle: {
                validators: {
                    notEmpty: {
                        message: 'Vui lòng nhập công việc'
                    }
                }
            },
            depID: {
                validators: {
                    notEmpty: {
                        message: 'Vui lòng nhập phòng ban'
                    },
                    regexp: {
                        regexp: /^\d+$/,
                        message: 'Vui lòng nhập phòng ban bằng số'
                    }
                }
            },
            account: {
                validators: {
                    notEmpty: {
                        message: 'Vui lòng nhập tài khoản'
                    }
                }
            },
            pass: {
                validators: {
                    notEmpty: {
                        message: 'Vui lòng nhập mật khẩu'
                    }
                }
            },
        }
    });
});

$('#modalEdit').on('hide.bs.modal', function () {
    $('#form-edit').bootstrapValidator('resetForm');
});

//collapse thay đổi chiều cao của modal khi bấm vào
//update lại chiều cao của nền đen
$('#acc-pem').on('shown.bs.collapse', function () {
    $('#modalEdit').modal('handleUpdate');
});

RED.ngApp.controller('userTestCtrl', function ($scope, $http, $apply, $timeout) {
    $scope.usersTest = [];
    $scope.ajax = {};
    $scope.checked = {};
    $scope.modalEdit;

    $scope.getUsersTest = function () {
        if ($scope.ajax.get)
            $scope.ajax.get.abort();

        $scope.ajax.get = $.ajax({
            url: CONFIG.siteUrl + '/rest/test',
            dataType: 'json'
        }).done(function (resp) {
            $apply(function () {
                $scope.usersTest = resp;
            });
        });
    };

    $scope.getUsersTest();

    $scope.getChecked = function () {
        var checked = [];
        for(var id in $scope.checked) {
            if($scope.checked[id]) {
                checked.push(id);
            }
        }
        return checked;
    };

    $scope.delete = function (id) {
        if(id) {
            $scope.checked = {};
            $scope.checked[id] = true;
        }

        if (!confirm('Bạn chắc chắn muốn xóa những đối tượng này?'))
            return;

        $http.delete(CONFIG.siteUrl + '/rest/test', {data: {'id': $scope.getChecked()}}).then(function (res) {
            $scope.getUsersTest();
            $scope.checked = {};
            $scope.checkedAll = false;
        });
    };

    $scope.edit = function (userTest) {
        userTest = userTest || {'id': 0};
        $scope.editing = $.extend({}, userTest);

        if(userTest.deleted) {
            $scope.editing.stt = userTest.deleted == 0;
        } else {
            $scope.editing.stt = false;
        }

        $timeout(function () {
            $($scope.modalEdit).modal('show');
        });
    };

    $scope.save = function () {
        var validator = $('#form-edit').data('bootstrapValidator');
        validator.validate();
        if (!validator.isValid())
            return;

        var userTest = $.extend({}, $scope.editing);
        userTest.deleted = $scope.editing.stt ? 0 : 1;

        $http.put(CONFIG.siteUrl + '/rest/test/' + userTest.id, userTest)
            .then(function (res) {
                $($scope.modalEdit).modal('hide');
                $scope.getUsersTest();
            });
    };

});