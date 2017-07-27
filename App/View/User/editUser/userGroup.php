<fieldset>
    <legend class="text-thin">Chọn nhóm</legend>

    <table class='table table-striped table-hover'>
        <tr ng-repeat="group in groups">
            <td>
                <label class="check">
                    <input type="checkbox" value="{{group.id}}" ng-checked="editingUser.groups.indexOf(group.id) != -1" ng-click="toggleGroup($event)"/>
                    <before></before>
                    <after></after>&nbsp;
                    {{group.groupName}}
                </label>
            </td>
        </tr>
    </table>
</fieldset>