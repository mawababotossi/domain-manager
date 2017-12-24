angular.module('app', ["app.http",'app.ctrls', 'ui.bootstrap','app.chart.directives', 'ngRoute'])
.config(function($routeProvider, $locationProvider) {
  //$locationProvider.html5Mode(true);
  $routeProvider
    .when('/domains',{ template: " ", controller: 'mainCtrl', animation: 'slide'})
    .when('/domains/:name',{ template: " ", controller: 'mainCtrl', animation: 'slide'})
    .when('/register',{ template: " ", controller: 'mainCtrl', animation: 'slide'})
    .when('/api',{ template: " ", controller: 'mainCtrl', animation: 'slide'})
    .when('/settings',{ template: " ", controller: 'mainCtrl', animation: 'slide'})
    .when('/applications',{ template: " ", controller: 'mainCtrl', animation: 'slide'})
    .otherwise({redirectTo: "/domains"})
});

angular.module('app.http', [], function($httpProvider) {
  // Use x-www-form-urlencoded Content-Type
  $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
  $httpProvider.defaults.headers.common['Authorization'] = "Bearer #doapikey#"; //Absolutly insecure
 
  /**
   * The workhorse; converts an object to x-www-form-urlencoded serialization.
   * @param {Object} obj
   * @return {String}
   */
  var param = function(obj) {
    var query = '', name, value, fullSubName, subName, subValue, innerObj, i;
      
    for(name in obj) {
      value = obj[name];
        
      if(value instanceof Array) {
        for(i=0; i<value.length; ++i) {
          subValue = value[i];
          fullSubName = name + '[' + i + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += param(innerObj) + '&';
        }
      }
      else if(value instanceof Object) {
        for(subName in value) {
          subValue = value[subName];
          fullSubName = name + '[' + subName + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += param(innerObj) + '&';
        }
      }
      else if(value !== undefined && value !== null)
        query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
    }
      
    return query.length ? query.substr(0, query.length - 1) : query;
  };
 
  // Override $http service's default transformRequest
  $httpProvider.defaults.transformRequest = [function(data) {
    return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
  }];
});

angular.module("app.ctrls", ['ngResource'])
.filter('getByProperty', function() {
    return function(propertyName, propertyValue, collection) {
        var i=0, len=collection.length;
        for (; i<len; i++) {
            if (collection[i][propertyName] == +propertyValue) {
                return collection[i];
            }
        }
        return null;
    }
})
.factory('mySocket', function (socketFactory) {
  return socketFactory();
})
.factory("UserService", ['$http', '$rootScope', function($http, $scope) {
  var endpoint = '/wp-admin';
  var doapi    = 'https://api.digitalocean.com/v2/domains';

  return {
    
    getUser: function(_cb) {
      if($scope.user) return _cb($scope.user);

      $scope.loading = true;
      $http.get(endpoint+"/admin-ajax.php?action=get_user_data").success(function(data){
        $scope.user = data;
        $scope.loading = false;
        return _cb($scope.user); 
      });
    },

    addDomainToCart: function(_domain,_cb){
      $http({
        url: endpoint+"/admin-ajax.php?action=add_domain_to_cart",
        method: "POST",
        data: _domain,
      }).success(function (data, status, headers, config) {
        return _cb(data); 
      }).error(function (data, status, headers, config) {
        $scope.status = status;
      });
    }, 
   
    deleteDomainFromCart: function(_domain,_cb){
      $http({
        url: endpoint+"/admin-ajax.php?action=delete_domain_from_cart",
        method: "POST",
        data: {'domain':_domain},
      }).success(function (data, status, headers, config) {
        return _cb(data); 
      }).error(function (data, status, headers, config) {
        $scope.status = status;
      });
    }, 

    getCartData: function(_cb) {
      //$scope.loading = true;
      $http.get(endpoint+"/admin-ajax.php?action=get_cart_data").success(function(data){
        return _cb(data); 
      });
    },

    saveCommand: function(_phone,_cb){
      $http({
        url: endpoint+"/admin-ajax.php?action=save_command",
        method: "POST",
        data: {'phone':_phone},
      }).success(function (data, status, headers, config) {
        return _cb(data); 
      }).error(function (data, status, headers, config) {
        $scope.status = status;
      });
    }, 

    whois: function(_domain,_cb){
      $scope.loading = true;
      $http({
        url: endpoint+"/admin-ajax.php?action=get_whois",
        method: "POST",
        dataType:'json',
        data: {'domain': _domain},
      }).success(function (data, status, headers, config) {
        $scope.loading = false;
        return _cb(data); 
      }).error(function (data, status, headers, config) {
        $scope.status = status;
        $scope.loading = false;
      });
    },

    dnsinfo: function(_domain,_cb){
      $scope.loading = true;
      $http({
        url: doapi+'/'+_domain+'/records',
        method: "GET",
        dataType:'json',
      }).success(function (data, status, headers, config) {
        $scope.loading = false;
        return _cb(data); 
      }).error(function (data, status, headers, config) {
        $scope.status = status;
        $scope.loading = false;
      });
    },

    getHistory: function(_cb){
      if($scope._domains) return _cb($scope._domains);
      $scope.loading = true;
      $scope._domains = [];
      $http.get(endpoint+"/admin-ajax.php?action=get_history").success(function(data){
        $scope._domains = data;
        $scope.loading = false;
        return _cb($scope._domains); 
      });
    }
  };
}])
.controller('mainCtrl', ['$scope','$rootScope', '$location', function($scope, $rootScope, $location) {
  var path = $location.path(); 
  $rootScope.isDomains = path==='/domains'
  $rootScope.isRegister = 0===path.indexOf('/register')? true:false;
  $rootScope.isDnsInfo = 0===path.indexOf('/domains/')? true:false;
  $rootScope.isAPI = path==='/api';
  $rootScope.isCart = path==='/cart';
  $rootScope.isReport = path==='/report'  
}])

.controller("sitePageCtrl",  ["$scope", "$rootScope", "$modal", "$log","UserService", function($scope,$rootScope, $modal, $log, UserService) {
     $scope.openSignupModal = function() {
        
        var modalInstance;
        
        modalInstance = $modal.open({templateUrl: "signupModalContent.html",controller: "signupModalInstanceCtrl"})
    }
  
 }])
.controller("signupModalInstanceCtrl", ["$scope", "$modalInstance", function($scope, $modalInstance) {
     
    $scope.cancel = function() {
        $modalInstance.dismiss("cancel")
    }
}])

.controller("loadingCtrl", ['$scope','$rootScope', function($scope, $rootScope){
  
 }])

.controller("userDataCtrl", ['$scope',"$rootScope",'UserService', function($scope, $rootScope, UserService){
  $rootScope.cartData = [];
  $scope.cartData = [];
  //UserService.getUser(function(data){});
  UserService.getCartData(function(data){$rootScope.cartData = data; $scope.cartData = [];}); //To retrieve user cart
}])

.controller("currentRunningTasksCtrl", ['$scope','UserService', function($scope, UserService){
  $scope.runningTasks = [];

     //setInterval(function(){
     //   UserService.getRunningTasks(function(data){
     //       $scope.runningTasks = data;
     //   });
     //}, 60000)
}])

.controller("registerDomainCtrl", ['$scope', '$rootScope','UserService', function($scope, $rootScope, UserService){
  $scope.avalaibilityCheck = {};
  $scope.domains = [];
  $scope.supportedExtensions = ['.tg','.com','.net','.info','.org'];
  $scope.cart = [];

  //if($rootScope.messageModel !== undefined) $scope.message = $rootScope.messageModel;

  $scope.checkDomainIsAvalaible = function() {
    var domains = $scope.avalaibilityCheck.domains.replace( /\n/g, " " ).split(" ");

    angular.forEach(domains, function(domain, key){
       if (/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/.test(domain) || /^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]$/.test(domain)) {
          //$scope.addDomain(domain); 
          $scope.suggestDomains(domain);
       }else{
          
       }
    });

    $scope.getDomainsStatusByWhois();
  };

  $scope.suggestDomains = function(domain){
      var prts = domain.split(".");
      angular.forEach($scope.supportedExtensions, function(ext, key){
         $scope.addDomain(prts[0]+ext);
      })
  }
  
  $scope.addDomain = function(_domainName){
    var inDomains = false;
    angular.forEach($scope.domains, function(domain, key){
       if(domain.name == _domainName) {
         inDomains = true;
       }
    });

    if(!inDomains) $scope.domains.push({'name':_domainName, 'status':'checking', 'inCart': false});

  };

  $scope.updateDomainStatus = function(){

     angular.forEach($scope.domains, function(domain, key){
        domain.inCart = false;
        angular.forEach($rootScope.cartData, function(elt, key){ 
          if (elt.name == domain.name) { domain.inCart = true;  console.log($scope.domains)}
        });
     });

  }
   
  $scope.getDomainsStatusByWhois = function(){
     angular.forEach($scope.domains, function(domain, key){
       // I want to get .tg domains to the top
       if (domain.name.endsWith(".tg")){
          domain.cctldtg = true;
          var a = $scope.domains.splice(key,1);   // removes the item
            $scope.domains.unshift(a[0]); 
       }

       if (domain.status == 'checking') {
          UserService.whois(domain.name, function(data){ 
             if(data.available == true) { domain.status = 'available'; } 
             else if(data.available == 'error') { domain.status = 'checking';} 
             else { domain.status = 'not_available';}
          }); console.log($scope.domains);
       }
     });
 
    $scope.updateDomainStatus();
  }

  $scope.addToCart = function(domain) {
     var _data = {'domain':domain};
     UserService.addDomainToCart(_data, function(data){ $rootScope.cartData = data; $scope.updateDomainStatus(); });
  };

  $scope.deleteDomainFromCart = function(_domainName){
      UserService.deleteDomainFromCart(_domainName, function(data){$scope.cartData = data; $rootScope.cartData = data;  $scope.updateDomainStatus();});
  }

  $scope.init = function(domains){
     $scope.avalaibilityCheck.domains = domains;
     $scope.checkDomainIsAvalaible();
  }

}])

.controller("showCartCtrl", ["$scope", "$rootScope","UserService", "$filter", function($scope, $rootScope, UserService, $filter) {
    $scope.cartData = [];
    UserService.getCartData(function(data){$scope.cartData = data; });

    $scope.deleteDomainFromCart = function(_domainName){
      UserService.deleteDomainFromCart(_domainName, function(data){$scope.cartData = data; $rootScope.cartData = data;});
    }
}])

.filter("sanitize", ['$sce', function($sce) {
  return function(htmlCode){
    return $sce.trustAsHtml(htmlCode);
  }
}])
.controller("DatepickerCtrl", ["$scope", function($scope) {
            return $scope.today = function() {
                return $scope.dt = new Date
            }, $scope.today(), $scope.showWeeks = !0, $scope.toggleWeeks = function() {
                return $scope.showWeeks = !$scope.showWeeks
            }, $scope.clear = function() {
                return $scope.dt = null
            }, $scope.disabled = function(date, mode) {
                return "day" === mode && (0 === date.getDay() || 6 === date.getDay())
            }, $scope.toggleMin = function() {
                var _ref;
                return $scope.minDate = null != (_ref = $scope.minDate) ? _ref : {"null": new Date}
            }, $scope.toggleMin(), $scope.open = function($event) {
                return $event.preventDefault(), $event.stopPropagation(), $scope.opened = !0
            }, $scope.dateOptions = {"year-format": "'yy'","starting-day": 1}, $scope.formats = ["dd-MMMM-yyyy", "yyyy/MM/dd", "shortDate"], $scope.format = $scope.formats[0]
        }])
.controller("TimepickerCtrl", ["$scope", function($scope) {
            return $scope.mytime = new Date, $scope.hstep = 1, $scope.mstep = 15, $scope.options = {hstep: [1, 2, 3],mstep: [1, 5, 10, 15, 25, 30]}, $scope.ismeridian = !0, $scope.toggleMode = function() {
                return $scope.ismeridian = !$scope.ismeridian
            }, $scope.update = function() {
                var d;
                return d = new Date, d.setHours(14), d.setMinutes(0), $scope.mytime = d
            }, $scope.changed = function() {
                return void 0
            }, $scope.clear = function() {
                return $scope.mytime = null
            }
        }])

.controller("historyCtrl", ["$scope", "$rootScope", "$filter","$modal", "UserService", function($scope, $rootScope, $filter, $modal, UserService) {
    var init;
    $scope.domains =  [];
    $scope.selectedDomains = {};

    UserService.getHistory(function(data){
    $scope.domains = data
    
    return $scope.domains, 
    $scope.searchKeywords = "", 
    $scope.filteredDomains = [], 
    $scope.row = "", 

    $scope.select = function(page) {
        var end, start;
        return start = (page - 1) * $scope.numPerPage, end = start + $scope.numPerPage, $scope.currentPageDomains = $scope.filteredDomains.slice(start, end)
    }, 
  
    $scope.onFilterChange = function() {
        return $scope.select(1), $scope.currentPage = 1, $scope.row = ""
    }, 
   
    $scope.onNumPerPageChange = function() {
        return $scope.select(1), $scope.currentPage = 1
    }, 

    $scope.onOrderChange = function() {
        return $scope.select(1), $scope.currentPage = 1
    }, 
  
    $scope.search = function() {
        return $scope.filteredDomains = $filter("filter")($scope.domains, $scope.searchKeywords), $scope.onFilterChange()
    }, 

    $scope.order = function(rowName) {
        return $scope.row !== rowName ? ($scope.row = rowName, $scope.filteredDomains = $filter("orderBy")($scope.domains, rowName), $scope.onOrderChange()) : void 0;
    },
    
    $scope.whoisDomain = function(_domain) {
	$scope.whoisContent = '';
	UserService.whois(_domain, function(data){ 
             if(data.whois) { $scope.whoisContent = data.whois; $scope.openHistoryModal(); } 
        });
        
    },
    
    $scope.openHistoryModal = function() {
        
        var modalInstance;
        
        modalInstance = $modal.open({templateUrl: "historyModalContent.html", controller: "HModalInstanceCtrl", resolve: {whois: function() {
             return $scope.whoisContent;
        }}})
    },    

    $scope.numPerPageOpt = [3, 5, 10, 20], $scope.numPerPage = $scope.numPerPageOpt[2], $scope.currentPage = 1, $scope.currentPageDomains = [], 
   
    (init = function() {
        return $scope.search(), $scope.select($scope.currentPage), $scope.order("-timeCreated")
    })()
  });
}])

.controller("HModalInstanceCtrl", ["$scope", "$filter", "$modalInstance", "whois", function($scope, $filter, $modalInstance, whois) {
    $scope.whoisContent = whois;

    $scope.close = function() {
        $modalInstance.dismiss("cancel")
    }
}])

.controller("dnsInfoCtrl", ['$scope','UserService','$routeParams', function($scope, UserService, $routeParams){
  $scope.domain_records = {};
  $scope.domain_name = $routeParams.name; //alert($scope.domain_name);
  UserService.dnsinfo($scope.domain_name, function(data){ 
     if(data) { 
        $scope.domain_records = data.domain_records;
        angular.forEach($scope.domain_records, function(record, key){
         record.hostname = record.name+'.'+$scope.domain_name;
         if(record.name == '@') record.hostname = $scope.domain_name; 
        }) 
     } 
  });

}])
.directive('hoversensitive',
   function() {
      return {
         link : function(scope, element, attrs) {
            element.parent().bind('mouseenter', function() {
                //element.show();
            });
            element.parent().bind('mouseleave', function() {
                 //element.hide();
            });
       }
   };
})
.directive('showonparenthover',
   function() {
      return {
         link : function(scope, element, attrs) {
            element.addClass('hide');
            element.parent().parent().bind('mouseenter', function() {
                element.removeClass('hide');
            });
            element.parent().parent().bind('mouseleave', function() {
                 element.addClass('hide');
            });
       }
   };
})
.directive('eatclick', function() {
    return function(scope, element, attrs) {
        $(element).click(function(event) {
            event.preventDefault(); event.stopPropagation();
        });
    }
})
.directive("highlightActive", [function() {
            return {restrict: "A",controller: ["$scope", "$element", "$attrs", "$location", function($scope, $element, $attrs, $location) {
                        var highlightActive, links, path;
                        return links = $element.find("a"), path = function() {
                            return $location.path()
                        }, highlightActive = function(links, path) {
                            return path = "#" + path, angular.forEach(links, function(link) {
                                var $li, $link, href;
                                return $link = angular.element(link), $li = $link.parent("li"), href = $link.attr("href"), $li.hasClass("active") && $li.removeClass("active"), 0 === path.indexOf(href) ? $li.addClass("active") : void 0
                            })
                        }, highlightActive(links, $location.path()), $scope.$watch(path, function(newVal, oldVal) {
                            return newVal !== oldVal ? highlightActive(links, $location.path()) : void 0
                        })
                    }]}
        }])
.filter('cut', function () {
        return function (value, wordwise, max, tail) {
            if (!value) return '';

            max = parseInt(max, 10);
            if (!max) return value;
            if (value.length <= max) return value;

            value = value.substr(0, max);
            if (wordwise) {
                var lastspace = value.lastIndexOf(' ');
                if (lastspace != -1) {
                    value = value.substr(0, lastspace);
                }
            }

            return value + (tail || ' …');
        };
    });

angular.module("app.chart.directives", [])
.directive("flotChart", [function() {
            return {restrict: "A",scope: {data: "=",options: "="},link: function(scope, ele) {
                    var data, options, plot;
                    return data = scope.data, options = scope.options, plot = $.plot(ele[0], data, options)
         }}
}]);
