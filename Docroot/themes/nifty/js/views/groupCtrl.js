$(function () {
    $('#form-edit').bootstrapValidator({
        excluded: [':disabled'],
        fields: {
            txtGroupCode: {
                validators: {
                    notEmpty: {
                        message: 'Vui lòng nhập mã nhóm'
                    }
                }
            },
            txtGroupName: {
                validators: {
                    notEmpty: {
                        message: 'Vui lòng nhập tên nhóm'
                    }
                }
            }
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


RED.ngApp.controller('groupCtrl', function ($scope, $timeout, $apply, $http) {
    $scope.groups = [];
    $scope.ajax = {};
    $scope.checked = {};
    $scope.modalEdit;
    $scope.editing;
    $scope.tab = 0;


    $scope.getChecked = function () {
        var checked = [];
        for (var i in $scope.checked)
            if ($scope.checked[i])
                checked.push(i);
        return checked;
    };

    $scope.setTab = function (idx) {
        $scope.tab = idx;
    };

    $scope.getGroups = function () {
        if ($scope.ajax.get)
            $scope.ajax.get.abort();

        $scope.ajax.get = $.ajax({
            url: CONFIG.siteUrl + '/rest/group',
            dataType: 'json'
        }).done(function (resp) {
            $apply(function () {
                $scope.groups = resp;
            });
        });
    };
    $scope.getGroups();

    $scope.getCheckedUsers = function () {
        var arr = [];
        if ($scope.editing && $scope.editing.checked)
            for (var i in $scope.editing.checked)
                if ($scope.editing.checked[i])
                    arr.push(i);
        return arr;
    };

    $scope.edit = function (group) {
        console.log('here');
        group = group || {'id': 0, 'stt': true};
        $scope.editing = $.extend({}, group);
        $scope.editing.checked = {};
        $scope.ajax = {};
        $scope.tab = 0;
        $scope.editing.permissions = $scope.editing.permissions || [];

        if (group.id != 0)
            $.ajax({
                'cache': false,
                'url': CONFIG.siteUrl + '/rest/group/' + group.id + '/user',
                'dataType': 'json'
            }).done(function (resp) {
                $apply(function () {
                    $scope.editing.users = resp;
                });
            });

        $http.get(CONFIG.siteUrl + '/rest/basePermission').then(function (resp) {
            $scope.permissions = resp.data;
        });

        $timeout(function () {
            $($scope.modalEdit).modal('show');
        });
    };

    $scope.pickUser = function () {
        var notUser = [];
        for (var i in $scope.editing.users)
            notUser.push($scope.editing.users[i].id);

        $('[ng-user-picker]')[0].openModal({
            notGroup: [$scope.editing.id],
            notUser: notUser,
            submit: function (users) {
                $apply(function () {
                    $scope.editing.users = $scope.editing.users || [];
                    $scope.editing.users = $scope.editing.users.concat(users);
                });
            }});
    };

    $scope.removeUser = function () {
        for (var i in $scope.editing.users)
            if ($scope.editing.checked[$scope.editing.users[i].id])
                $scope.editing.users.splice(i, 1);
        $scope.editing.checked = {};
    };



    $scope.save = function () {
        var validator = $('#form-edit').data('bootstrapValidator');
        validator.validate();
        if (!validator.isValid())
            return;

        var group = $.extend({}, $scope.editing);
        group.users = [];
        for (var i in $scope.editing.users)
            group.users.push($scope.editing.users[i].id);

        $scope.ajax.save = true;

        $http.put(CONFIG.siteUrl + '/rest/group/' + group.id, group).then(function (resp) {
            $scope.ajax.save = null;
            if (resp.data.status) {
                $($scope.modalEdit).modal('hide');
                $scope.getGroups();
            } else if (resp.data.error == 'duplicateCode') {
                alert('Trùng mã nhóm');
            }
        });
    };

    $scope.delete = function (id) {
        if (id) {
            $scope.checked = {};
            $scope.checked[id] = true;
        }

        if (!confirm('Bạn chắc chắn muốn xóa những đối tượng này?'))
            return;

        $http.delete(CONFIG.siteUrl + '/rest/group', {data: {'id': $scope.getChecked()}}).then(function () {
            $scope.getGroups();
        });
    };

    $scope.togglePermission = function ($event) {
        var target = $event.target;
        if (target.checked && $scope.editing.permissions.indexOf(target.value) == -1)
            $scope.editing.permissions.push(target.value);
        else if (!target.checked) {
            var idx = $scope.editing.permissions.indexOf(target.value);
            if (idx != -1)
                $scope.editing.permissions.splice(idx, 1);
        }
    };
});

