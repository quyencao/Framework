<fieldset>
    <legend class="text-thin">Tài khoản</legend>
    <div class="form-group">
        <label class="control-label col-xs-3" for="user-account"><?php echo static::HTML_REQUIRED ?> Đăng nhập:</label>
        <div class="col-xs-9">
            <div style="display: flex">
                <div style="flex: 1;">
                    <input type="text" id="user-account" class="form-control" ng-model="editingUser.account" 
                           name="account"
                           required minlength="3" ng-dom="userAccDom"
                           data-bv-notempty-message="Vui lòng nhập tài khoản"
                           data-bv-stringlength-message="Độ dài tối thiểu 3 ký tự"
                           />
                </div>
                <button class="btn btn-purple" type="button" ng-click="checkUniqueAcc()">Kiểm tra</button>
            </div>

        </div>
    </div>
    <div class='form-group' ng-if="!editingUser.isAdmin">
        <div class='col-xs-9 col-xs-offset-3'>
            <label class="check" ng-if="!editingUser.isAdmin">
                <input type="checkbox" ng-model="editingUser.stt" />
                &nbsp;
                Hoạt động
            </label>
        </div>
    </div>
    <div class='form-group' ng-show="!editingUser.changePass">
        <div class='col-xs-9 col-xs-offset-3'>
            <div >
                <div class="help-block">
                    <a href="javascript:;" class="btn-link" ng-click="togglePassword()">Đổi mật khẩu</a>
                </div>
            </div>
        </div>
    </div>
    <div ng-show="editingUser.changePass">
        <div class="form-group">
            <label class="control-label col-xs-3" for="user-new-pass"><span ng-if="!editingUser.id"><?php echo static::HTML_REQUIRED ?> </span>Mật khẩu mới:</label>
            <div class="col-xs-9">
                <input type="password" id="user-new-pass" class="form-control" ng-model="editingUser.newPass" 
                       name="newPass"
                       ng-required="!editingUser.id"
                       data-bv-notempty-message="Vui lòng nhập mật khẩu"
                       data-bv-stringlength-min="6"
                       data-bv-stringlength-message="Độ dài tối thiểu 6 ký tự"
                       ng-dom="newPassDom"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-xs-3" for="user-re-pass">Nhập lại mật khẩu:</label>
            <div class="col-xs-9">
                <input type="password" id="user-re-pass" class="form-control" 
                       ng-model="editingUser.rePass" 
                       name="rePass"
                       data-bv-identical-field='newPass'
                       data-bv-identical-message='Mật khẩu nhập lại không khớp'
                       ng-dom="rePassDom"/>
                <div class="help-block">
                    <div class="pull-left">
                        <a href="javascript:;" class="btn-link" ng-click="togglePassword()" ng-if="editingUser.id">Hủy đổi mật khẩu</a>&nbsp;
                    </div>
                    <div class="pull-right">
                        <span ng-if="editingUser.passError">{{editingUser.passError}}</span>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</fieldset>
<fieldset>
    <legend class="text-thin">Thông tin hành chính</legend>

    <div class="form-group">
        <label class="control-label col-xs-3" for="user-dep">Thuộc đơn vị:</label>
        <div class="col-xs-9">
            <div class="parent-dep">
                <input type="text" class="form-control" id="user-dep" readonly placeholder="Chọn đơn vị"
                       value="{{editingUser.parentDep.depName}}" ng-click="pickUserDep()"/>
                <span ng-if="editingUser.parentDep && editingUser.parentDep.id" ng-click="clearUserDep()">&times;</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-3" for="user-name"><?php echo static::HTML_REQUIRED ?> Họ và tên:</label>
        <div class="col-xs-9">
            <input 
                type="text" id="user-name" class="form-control" ng-model="editingUser.fullName"
                required name="fullName"
                data-bv-notempty-message="Vui lòng nhập họ tên"
                />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-3" for="user-job">Chức vụ:</label>
        <div class="col-xs-9">
            <input type="text" id="user-job" class="form-control" ng-model="editingUser.jobTitle"/>
        </div>
    </div>
</fieldset>