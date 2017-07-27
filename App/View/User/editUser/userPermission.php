<div class="panel-group accordion" id="acc-pem">
    <div class="panel panel-info panel-bordered" ng-repeat="app in permissions">

        <!--Accordion title-->
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-parent="#acc-pem" data-toggle="collapse" href="#acc-pem-{{$index}}">{{app.name}}</a>
            </h4>
        </div>

        <!--Accordion content-->
        <div class="panel-collapse collapse" id="acc-pem-{{$index}}">
            <div class="panel-body">
                <fieldset ng-repeat="group in app.groups">
                    <legend class="text-thin">{{group.name}}</legend>

                    <table class='table table-striped table-record'>
                        <tr ng-repeat="pem in group.permissions">
                            <td>
                                <label class="check" value="pem.name">
                                    <input type="checkbox" value="{{pem.id}}" ng-checked="editingUser.permissions.indexOf(pem.id) != -1" ng-click="togglePermission($event)"/>
                                    <before></before>
                                    <after></after>&nbsp;
                                    {{pem.name}}
                                </label>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>
        </div>
    </div>
</div>