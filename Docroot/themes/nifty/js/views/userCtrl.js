var userValidator = {
    excluded: [':disabled'],
    fields: {
        newPass: {
            validators: {
                identical: {
                    field: 'rePass',
                    message: 'Mật khẩu nhập lại không khớp'
                }
            }
        },
        rePass: {
            validators: {
                identical: {
                    field: 'newPass',
                    message: 'Mật khẩu nhập lại không khớp'
                }
            }
        }
    }
};
$('#form-user').bootstrapValidator(userValidator);

//reset báo lỗi (nếu có) khi mở modal mới
$('#modal-user').on('hide.bs.modal', function () {
    $('#form-user').bootstrapValidator('resetForm');
});

function reBalanceCols() {
    var left = $('.tree-left .panel-body');
    var right = $('.tree-right .panel-body');
    //reset
    left.css({height: 'auto'});
    right.css({height: 'auto'});
    //height
    var leftHeight = left.height();
    var rightHeight = right.height() + 50;
    if (leftHeight > rightHeight)
        right.css({height: leftHeight});
    else
        left.css({height: rightHeight});
}

//collapse thay đổi chiều cao của modal khi bấm vào
//update lại chiều cao của nền đen
$('#acc-pem').on('shown.bs.collapse', function () {
    $('#modal-user').modal('handleUpdate');
});

RED.ngApp.controller('userCtrl', function ($scope, $apply, $timeout, $http) {
    $scope.depID;
    $scope.department = null;
    $scope.ajax = {};
    $scope.editingDep;
    $scope.editingUser;
    $scope.modalDep;
    $scope.checkedUsers = {};
    $scope.checkedDeps = {};
    $scope.departmentPicker;
    $scope.groups;
    $scope.permissions;
    $scope.ajax = {};
    $scope.searchResult;

    $(window).on('hashchange', function () {
        $apply(function () {
            $scope.filter = {'search': ''};
            $scope.depID = window.location.hash.replace('#', '').replace('/', '') || 0;
            $scope.getDep($scope.depID);
        });
    }).trigger('hashchange');

    $scope.getDep = function (depID) {
        if ($scope.ajax.load)
            $scope.ajax.load.abort();

        $scope.ajax.load = $.ajax({
            'url': CONFIG.siteUrl + '/rest/department/' + depID,
            'cache': false,
            'data': {'users': 1, 'departments': 1, 'ancestors': 1},
            'dataType': 'json'
        }).done(function (res) {
            $apply(function () {
                $scope.department = res;
                $timeout(reBalanceCols);
            });
        }).always(function () {
            $scope.ajax.load = false;
        });
    };

    $scope.deps = function () {
        var dep = $scope.department || {};
        return $scope.searchResult ? $scope.searchResult.departments : dep.deps;
    };

    $scope.users = function () {
        var dep = $scope.department || {};
        return $scope.searchResult ? $scope.searchResult.users : dep.users;
    };

    $scope.toggleFilter = function () {
        $scope.showFilter = !$scope.showFilter;
    };

    $scope.$watchCollection('filter', function (newVal) {
        if ($scope.ajax.search)
            $scope.ajax.search.abort();
        if (!newVal)
            return;
        if (newVal.search) {
            $scope.ajax.search = $.ajax({
                'url': CONFIG.siteUrl + '/rest/user/search',
                'data': $scope.filter
            }).done(function (resp) {
                $apply(function () {
                    $scope.searchResult = resp;
                    $timeout(function () {
                        reBalanceCols();
                    });
                });
            });
        } else {
            $scope.searchResult = null;
            $timeout(function () {
                reBalanceCols();
            });
        }
    });

    $scope.$watch('depID', function (newVal) {
        if (typeof newVal === 'undefined')
            return;
    });

    $scope.goUp = function () {
        if (!$scope.department.ancestors.length) {
            window.location.hash = '#/';
            return;
        }
        var parentDep = $scope.department.ancestors[$scope.department.ancestors.length - 1];
        window.location.hash = '#/' + parentDep.id;
    };

    $scope.editDep = function (dep, insertAndOpen) {
        dep = dep || {'stt': true};
        dep.insertAndOpen = insertAndOpen || false;
        $scope.editingDep = $.extend({}, dep);
        $scope.editingDep.parentDep = $scope.department;
        $($scope.modalDep).modal('show');
    };

    $scope.anythingChecked = function () {
        for (var i in $scope.checkedDeps)
            if ($scope.checkedDeps[i])
                return true;
        for (var i in $scope.checkedUsers)
            if ($scope.checkedUsers[i])
                return true;
        return false;
    };

    $scope.pickEditDep = function () {
        $('[ng-department-picker]')[0].openModal({
            'selected': $scope.editingDep.parentDep ? $scope.editingDep.parentDep.id : null,
            'not': $scope.editingDep.id,
            'submit': function (dep) {
                $apply(function () {
                    $scope.editingDep.parentDep = dep;
                });
            }
        });
    };

    $scope.checkUniqueAcc = function () {
        var url = CONFIG.siteUrl + '/rest/user/checkUniqueAccount';
        var data = {id: $scope.editingUser.id, account: $scope.editingUser.account};

        $.getJSON(url, data, function (resp) {
            if (resp.valid)
                alert('Tài khoản khả dụng');
            else
                alert('Tài khoản bị trùng');

        });
    };

    $scope.clearParentDep = function () {
        $scope.editingDep.parentDep = null;
        $scope.editingDep.depID = null;
    };

    $scope.$watchCollection('editingDep', function (newVal) {
        if (!newVal)
            return;
        if (newVal.parentDep)
            newVal.depID = newVal.parentDep.id;
        newVal.id = newVal.id || 0;
    });

    $scope.submitDep = function ($event) {
        if (!$event.target.checkValidity()) {
            return;
        }
        var url = CONFIG.siteUrl + '/rest/department/' + $scope.editingDep.id;
        $scope.ajax.submit = true;
        $http.put(url, $scope.editingDep).then(function () {
            $scope.getDep($scope.depID);
            $($scope.modalDep).modal('hide');
            $scope.ajax.submit = false;

            if ($scope.editingDep.insertAndOpen)
                $timeout(function () {
                    $scope.editDep(null, true);
                }, 500);

            //reload lai cot trai
            $scope.getDepTree();
        });
    };

    $scope.editUser = function (user, insertAndOpen) {
        user = user ? $.extend({}, user) : {
            'stt': true,
            'changePass': true,
            'groups': [],
            'permissions': []
        };

        user.insertAndOpen = insertAndOpen || false;
        user.parentDep = $scope.department;
        $scope.editingUser = user;
        $($scope.modalUser).modal('show');

        $http.get(CONFIG.siteUrl + '/rest/group?status=1').then(function (resp) {
            $scope.groups = resp.data;
        });
        $http.get(CONFIG.siteUrl + '/rest/basePermission').then(function (resp) {
            $scope.permissions = resp.data;
        });
    };

    $scope.togglePassword = function () {
        if ($scope.editingUser.changePass) {
            $scope.editingUser.changePass = false;
            $scope.editingUser.newPass = null;
            $scope.editingUser.rePass = null;
        } else {
            $scope.editingUser.changePass = true;
        }
        $timeout(function () {
            $(window).trigger('resize');
        });
    };

    $scope.pickUserDep = function () {
        $('[ng-department-picker]')[0].openModal({
            'selected': $scope.editingUser.parentDep ? $scope.editingUser.parentDep.id : null,
            'submit': function (dep) {
                $apply(function () {
                    $scope.editingUser.parentDep = dep;
                });
            }
        });
    };
    $scope.$watchCollection('editingUser', function (newVal) {
        if (!newVal)
            return;
        newVal.depID = newVal.parentDep ? newVal.parentDep.id : 0;
        newVal.id = newVal.id || 0;
    });

    $scope.clearUserDep = function () {
        $scope.editingUser.parentDep = null;
    };

    $scope.submitUser = function ($event) {
        var form = $('#form-user');
        form.bootstrapValidator({
            excluded: [':disabled']
        });
        var validator = form.data('bootstrapValidator');
        validator.validate();
        if (!validator.isValid())
            return;

        var user = $scope.editingUser;
        user.passError = null;

        var url = CONFIG.siteUrl + '/rest/user/' + (user.id ? user.id : 0);
        $scope.ajax.submit = true;
        $http.put(url, user).then(function (resp) {
            $($scope.modalUser).modal('hide');
            $scope.getDep($scope.depID);
            $scope.ajax.submit = false;

            if ($scope.editingUser.insertAndOpen) {
                $timeout(function () {
                    $scope.editUser(null, true);
                }, 500);
            }
            $scope.editingUser = null;
        });
    };

    $scope.toggleGroup = function ($event) {
        var target = $event.target;
        if (target.checked && $scope.editingUser.groups.indexOf(target.value) == -1)
            $scope.editingUser.groups.push(target.value);
        else if (!target.checked) {
            var idx = $scope.editingUser.groups.indexOf(target.value);
            if (idx != -1)
                $scope.editingUser.groups.splice(idx, 1);
        }
    };

    $scope.togglePermission = function ($event) {
        var target = $event.target;
        if (target.checked && $scope.editingUser.permissions.indexOf(target.value) == -1)
            $scope.editingUser.permissions.push(target.value);
        else if (!target.checked) {
            var idx = $scope.editingUser.permissions.indexOf(target.value);
            if (idx != -1)
                $scope.editingUser.permissions.splice(idx, 1);
        }
    };

    $scope.getCheckedUsers = function () {
        var users = [];
        for (var id in $scope.checkedUsers)
            if ($scope.checkedUsers[id])
                users.push(id);

        return users;
    };

    $scope.getCheckedDeps = function () {
        var deps = [];
        for (var id in $scope.checkedDeps)
            if ($scope.checkedDeps[id])
                deps.push(id);

        return deps;
    };

    $scope.delete = function () {
        var msg = 'Những phòng ban, tài khoản trong đối tượng bị xóa sẽ chuyển về thư mục gốc, thực hiện?';

        if (confirm(msg)) {
            $http.delete(CONFIG.siteUrl + '/rest/user', {'data': $scope.getCheckedUsers()}).then(function () {
                return $http.delete(CONFIG.siteUrl + '/rest/department', {'data': $scope.getCheckedDeps()});
            }).then(function () {
                $scope.getDep($scope.depID);
            });
        }
    };

    $scope.move = function () {
        $('[ng-department-picker]')[0].openModal({
            'not': $scope.getCheckedDeps(),
            'selected': $scope.depID,
            'submit': function (dep) {
                if (dep.id == $scope.depID)
                    return;
                $http.put(CONFIG.siteUrl + '/rest/department/move', {'ids': $scope.getCheckedDeps(), 'dest': dep.id})
                        .then(function () {
                            return $http.put(CONFIG.siteUrl + '/rest/user/move', {'ids': $scope.getCheckedUsers(), 'dest': dep.id});
                        })
                        .then(function () {
                            $scope.getDep($scope.depID);
                            $scope.checkedDeps = {};
                            $scope.checkedUsers = {};
                        });
            }
        });
    };

    $scope.deleteUser = function (user) {
        $scope.checkedUsers = {};
        $scope.checkedDeps = {};
        $scope.checkedUsers[user.id] = true;

        $timeout(function () {
            $scope.delete();
        });

    };

    $scope.deleteDep = function (dep) {
        $scope.checkedUsers = {};
        $scope.checkedDeps = {};
        $scope.checkedDeps[dep.id] = true;

        $timeout(function () {
            $scope.delete();
        });
    };


    $scope.copyUser = function (user) {
        var u = $.extend({}, user);
        $.extend(u, {
            'id': 0,
            'isAdmin': 0,
            'account': '',
            'fullName': '',
            'changePass': true
        });
        $scope.editUser(u);
    };

    //cây thư mục bên trái
    $scope.root = {
        id: 0,
        depName: '[Thư mục gốc]'
    };

    $scope.getDepTree = function () {
        var params = {
            'departments': 1,
            'rescusively': 1
        };
        var url = CONFIG.siteUrl + '/rest/department/0?' + $.param(params);
        $.getJSON(url, function (res) {
            function findSelected(dep) {
                var ret = false;
                for (var i in dep.deps)
                    if (findSelected(dep.deps[i])) {
                        dep.expand = true;
                        ret = true;
                    }

                if (dep.id == $scope.depID) {
                    $scope.selected = dep;
                    ret = true;
                }
                return ret;
            }

            $apply(function () {
                findSelected(res);
                $scope.depTree = res;

                //tính lại chiều cao 2 cột
                $timeout(function () {
                    reBalanceCols();
                });
            });
        });
    };
    $scope.getDepTree();

    $scope.toggleExpand = function (dep) {
        dep.expand = !dep.expand;
    };

    $scope.setSelected = function (dep, $event) {
        $event.stopPropagation();
        var target = $($event.target);
        if (target.hasClass('fa-caret-down') || target.hasClass('fa-caret-right'))
            return;

        window.location.hash = '/' + dep.id;
    };

});

