"use strict";
var app = angular.module('ng-laravel');
app.controller('AdminCtrl',function($scope,$auth,hotkeys,$state,$translate,$rootScope,$translatePartialLoader,uibPaginationConfig,trans){

    /* show loading on page change */
    $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams) {
        if (toState.resolve) {
            $scope.loader = true;
        }
    });
    $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
        if (toState.resolve) {
            $scope.loader = false;
        }
    });

    /* Get user profile info */
    $scope.profile = $auth.getProfile().$$state.value;

    /* Define keyboard short-key */
    hotkeys.add({
        combo: 'ctrl+b',
        description: 'Open Request List',
        callback: function() {
            $state.go("admin.tasks");
        }
    });

    /* Search Input & Per Page toggle */
    $scope.searchShow = false;
    $scope.perPageShow = false;



    /* Change Language Function*/
    $scope.changeLanguage = function (langKey) {
        $rootScope.currentLanguage = $translate.use(langKey);
    };
    /* get available langKey */
    $scope.AvailableLanguageKeys = $translate.getAvailableLanguageKeys();

    /* Show loading on translate switch */
    $rootScope.$on('$translateChangeStart', function () {
        $scope.transLoader = true;
    });
    $rootScope.$on('$translateChangeSuccess', function() {
        $scope.transLoader = false;

        // ui-pagination translate
        uibPaginationConfig.firstText = $translate.instant('app.shared.paging.first');
        uibPaginationConfig.previousText = $translate.instant('app.shared.paging.pre');
        uibPaginationConfig.nextText = $translate.instant('app.shared.paging.next');
        uibPaginationConfig.lastText = $translate.instant('app.shared.paging.last');

        // populate sweet alert
        $rootScope.areYouSureDelete ={
            title: $translate.instant('app.shared.alert.areYouSure'),
            text: $translate.instant('app.shared.alert.areYouSureDescription'),
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: $translate.instant('app.shared.alert.confirmButtonText'),
            cancelButtonText: $translate.instant('app.shared.alert.cancelButtonText'),
            closeOnConfirm: false,
            closeOnCancel: true,
            showLoaderOnConfirm: true
        };

        // populate sweet alert
        $rootScope.recordDeleted = {
            title: $translate.instant('app.shared.alert.deletedTitle'),
            text: $translate.instant('app.shared.alert.successDeleted'),
            type:"success",
            confirmButtonText: $translate.instant('app.shared.alert.okConfirm'),
        };

        // populate sweet alert
        $rootScope.recordNotDeleted = {
            title: $translate.instant('app.shared.alert.errorDeleteTitle'),
            text: $translate.instant('app.shared.alert.errorDeleteDescription'),
            type:"error",
            confirmButtonText: $translate.instant('app.shared.alert.okConfirm'),
        };

        var htmlInputForm = '<div class="radio radio-primary"> <input type="radio" name="exportSelect" id="radio1" ng-model="radioValue"  value="1" checked> <label for="radio1">'+$translate.instant('app.shared.alert.selectWholeRecords')+'</label> </div> <div class="radio radio-primary"> <input type="radio" name="exportSelect" id="radio2" ng-model="radioValue" value="2"> <label for="radio2"> '+ $translate.instant('app.shared.alert.selectSelectedRecords')+' </label> </div>';
        // populate sweet alert
        $rootScope.exportSelect = {
            title: $translate.instant('app.shared.alert.exportSelectTitle'),
            text: htmlInputForm ,
            html: true,
            showCancelButton: true,
            confirmButtonText: $translate.instant("app.shared.alert.downloadExport"),
            confirmButtonColor: "#006DCC",
            cancelButtonText: $translate.instant('app.shared.alert.cancelAlert'),
            closeOnConfirm: false,
            closeOnCancel: true,
            //showLoaderOnConfirm: true
        };

        // populate sweet alert
        $rootScope.selectFileError = {
            title: $translate.instant('app.shared.alert.selectFileErrorTitle'),
            text: $translate.instant('app.shared.alert.selectFileError'),
            type:"error",
            confirmButtonText: $translate.instant('app.shared.alert.okConfirm')
        };
    });
    
});
