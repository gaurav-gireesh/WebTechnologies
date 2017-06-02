  
        window.fbAsyncInit = function() {
            FB.init({
                appId: '1691655584460409',
                version: 'v2.8'
            });
            /*FB.AppEvents.logPageView();*/
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

   
   //SUPER GLOBAL VARIABLES declaration
        /* var t = "13:56"; 
var cdt = moment(t, 'HH:mm');
alert(cdt.toDate());
alert(cdt.format('YYYY-MM-DD HH:mm:ss'))*/
        var clientLocationLat = "";
        var clientLocationLong = "";
        var options = {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        };

        //calling geolocator object from navigator object
        function success(pos) {
            var currd = pos.coords;
            console.log(`Latitude is:${currd.latitude}`);
            console.log(`Longitude is ${currd.longitude}`);
            //alert("Value of lat is"+`${currd.latitude}`);
            clientLocationLat = `${currd.latitude}`;
            clientLocationLong = `${currd.longitude}`;
            // alert(clientLocationLat+" and "+clientLocationLong);

        }

        function error(err) {
            console.warn(`ERROR(${err.code}):${err.message}`);
        }
        navigator.geolocation.getCurrentPosition(success, error, options);
        // Using Angular JS now to generate the result table


        function makeActive(this1) {
           
            if ($("#page").attr("class").includes('active'))
                $("#page").removeClass('active');
            if ($("#user").attr("class").includes('active'))
                $("#user").removeClass('active');
            if ($("#event").attr("class").includes('active'))
                $("#event").removeClass('active');
            if ($("#group").attr("class").includes('active'))
                $("#group").removeClass('active');
            if ($("#place").attr("class").includes('active'))
                $("#place").removeClass('active');
            if ($("#favorite").attr("class").includes('active'))
                $("#favorite").removeClass('active');
            $(this1).addClass( "active");
            
        }

        app = angular.module('resultApp', ['ngAnimate']);
        app.controller('resultAppController', ['$scope', '$http',
                function($scope, $http) {

                       $scope.next = $scope.prev = null;$scope.fbData=null;
                       $scope.placeData=$scope.eventData=$scope.userData=$scope.groupData=$scope.pageData=null; $scope.placeNext=$scope.placePrev=$scope.userPrev=$scope.userNext=$scope.groupNext=$scope.groupPrev=null;
                        $scope.pagePrev=$scope.pageNext=$scope.eventPrev=$scope.eventNext=null;
                    $scope.localStrorage = localStorage;
                     $("#resultTable").css("display", "none");
                    $scope.loadData = function(key) {
                    $("#fbinputText").blur();
                        if (key != null && key != "" && key!="undefined" &&$scope.searchType!="favorites") {
                      
                        $("#detailPage").hide("");
                        $("#favTable").hide("");

                        $scope.localStorage = localStorage;
                        
                         $("#resultTable").css("display", "none");
                         $("#pgbar1").css("display", "block");
                        $outType = $scope.searchType.substr(0, $scope.searchType.length - 1);

                       

                            if ($scope.searchType != "places") {
                                $http.get("http://gghw6-161921.appspot.com?key=" + $scope.searchKey + "&type=" + $outType)
                                    //$http.get("http://localhost:10080?key=" + $scope.searchKey + "&type=" + $outType)
                                    .then(function(response, $compile) {
                                        $("#pgbar1").css("display", "none");

                                        $scope.fbData = response.data.data;
                                        if (response.data.paging != null) {
                                            $scope.next = response.data.paging.next;
                                            if (response.data.paging.previous != null)
                                                $scope.prev = response.data.paging.previous;
                                            
                                            


                                        }
                                        
                                        $("#resultTable").show("slow");
                                    
                                    
                                    

                                    });
                                
                                
                                 //Calling other types for loading other tabs Meanwhile
                                    {
                                        //calling for the user 
                                        
                                         $http.get("http://gghw6-161921.appspot.com?key=" + $scope.searchKey + "&type=" +"user")
                                    //$http.get("http://localhost:10080?key=" + $scope.searchKey + "&type=" + $outType)
                                    .then(function(response, $compile) {
                                        //$("#pgbar1").css("display", "none");
                                                 $scope.userData=response.data.data;
                                        
                                        if (response.data.paging != null) {
                                            $scope.userNext = response.data.paging.next;
                                            if (response.data.paging.previous != null)
                                                $scope.userPrev = response.data.paging.previous;
                                            
                                           



                                        }});
                                        //FOR EVENTS
                                        $http.get("http://gghw6-161921.appspot.com?key=" + $scope.searchKey + "&type=" +"event")
                                    //$http.get("http://localhost:10080?key=" + $scope.searchKey + "&type=" + $outType)
                                    .then(function(response, $compile) {
                                        //$("#pgbar1").css("display", "none");
                                                 $scope.eventData=response.data.data;
                                        
                                        if (response.data.paging != null) {
                                            $scope.eventNext = response.data.paging.next;
                                            if (response.data.paging.previous != null)
                                                $scope.eventPrev = response.data.paging.previous;
                                            
                                           



                                        }});
                                        
                                        //FOR GROUPS
                                        $http.get("http://gghw6-161921.appspot.com?key=" + $scope.searchKey + "&type=" +"group")
                                    //$http.get("http://localhost:10080?key=" + $scope.searchKey + "&type=" + $outType)
                                    .then(function(response, $compile) {
                                        //$("#pgbar1").css("display", "none");
                                                 $scope.groupData=response.data.data;
                                        
                                        if (response.data.paging != null) {
                                            $scope.groupNext = response.data.paging.next;
                                            if (response.data.paging.previous != null)
                                                $scope.groupPrev = response.data.paging.previous;
                                            
                                           



                                        }});
                                        
                                        //FOR PAGES
                                        
                                        $http.get("http://gghw6-161921.appspot.com?key=" + $scope.searchKey + "&type=" +"page")
                                    //$http.get("http://localhost:10080?key=" + $scope.searchKey + "&type=" + $outType)
                                    .then(function(response, $compile) {
                                        //$("#pgbar1").css("display", "none");
                                                 $scope.pageData=response.data.data;
                                        
                                        if (response.data.paging != null) {
                                            $scope.pageNext = response.data.paging.next;
                                            if (response.data.paging.previous != null)
                                                $scope.pagePrev = response.data.paging.previous;
                                            
                                           



                                        }});
                                        //FOR PLACES
                                        $http.get("http://gghw6-161921.appspot.com?key=" + $scope.searchKey + "&type=" + "place" + "&lat=" + clientLocationLat + "&long=" + clientLocationLong)
                                    //$http.get("http://localhost:10080?key=" + $scope.searchKey + "&type=" + $outType)
                                    .then(function(response, $compile) {
                                        //$("#pgbar1").css("display", "none");
                                                 $scope.placeData=response.data.data;
                                        
                                        if (response.data.paging != null) {
                                            $scope.placeNext = response.data.paging.next;
                                            if (response.data.paging.previous != null)
                                                $scope.placePrev = response.data.paging.previous;
                                            
                                        }});
                                        
                                        
                                        
                                        
                                        
                                        
                                    }
                                
                                
                            } else {
                                
                                
                                $http.get("http://gghw6-161921.appspot.com?key=" + $scope.searchKey + "&type=" + $outType + "&lat=" + clientLocationLat + "&long=" + clientLocationLong)
                                    //$http.get("http://localhost:10080?key=" + $scope.searchKey + "&type=" + $outType + "&lat=" + clientLocationLat + "&long=" + clientLocationLong)
                                    .then(function(response, $compile) {
                                        $("#pgbar1").hide();

                                        $scope.fbData = response.data.data;
                                        if (response.data.paging != null) {
                                            $scope.next = response.data.paging.next;
                                            if (response.data.paging.previous != null)
                                                $scope.prev = response.data.paging.previous;
                                            
                                            $scope.placeData=$scope.fbData;
                                            $scope.placePrev=$scope.prev;
                                            $scope.placeNext=$scope.next;



                                        }

                                        $("#resultTable").show("slow");

                                    });
                                
                                
                                
                                 //calling for the user 
                                        
                                         $http.get("http://gghw6-161921.appspot.com?key=" + $scope.searchKey + "&type=" +"user")
                                    //$http.get("http://localhost:10080?key=" + $scope.searchKey + "&type=" + $outType)
                                    .then(function(response, $compile) {
                                        //$("#pgbar1").css("display", "none");
                                                 $scope.userData=response.data.data;
                                        
                                        if (response.data.paging != null) {
                                            $scope.userNext = response.data.paging.next;
                                            if (response.data.paging.previous != null)
                                                $scope.userPrev = response.data.paging.previous;
                                            
                                           



                                        }});
                                        //FOR EVENTS
                                        $http.get("http://gghw6-161921.appspot.com?key=" + $scope.searchKey + "&type=" +"event")
                                    //$http.get("http://localhost:10080?key=" + $scope.searchKey + "&type=" + $outType)
                                    .then(function(response, $compile) {
                                        //$("#pgbar1").css("display", "none");
                                                 $scope.eventData=response.data.data;
                                        
                                        if (response.data.paging != null) {
                                            $scope.eventNext = response.data.paging.next;
                                            if (response.data.paging.previous != null)
                                                $scope.eventPrev = response.data.paging.previous;
                                            
                                        }});
                                        
                                        //FOR GROUPS
                                        $http.get("http://gghw6-161921.appspot.com?key=" + $scope.searchKey + "&type=" +"group")
                                    //$http.get("http://localhost:10080?key=" + $scope.searchKey + "&type=" + $outType)
                                    .then(function(response, $compile) {
                                        //$("#pgbar1").css("display", "none");
                                                 $scope.groupData=response.data.data;
                                        
                                        if (response.data.paging != null) {
                                            $scope.groupNext = response.data.paging.next;
                                            if (response.data.paging.previous != null)
                                                $scope.groupPrev = response.data.paging.previous;
                                            
                                           



                                        }});
                                        
                                        //FOR PAGES
                                        
                                        $http.get("http://gghw6-161921.appspot.com?key=" + $scope.searchKey + "&type=" +"page")
                                    //$http.get("http://localhost:10080?key=" + $scope.searchKey + "&type=" + $outType)
                                    .then(function(response, $compile) {
                                        //$("#pgbar1").css("display", "none");
                                                 $scope.pageData=response.data.data;
                                        
                                        if (response.data.paging != null) {
                                            $scope.pageNext = response.data.paging.next;
                                            if (response.data.paging.previous != null)
                                                $scope.pagePrev = response.data.paging.previous;
                                            
                                           



                                        }});
                                
                                
                                
                                
                                

                            }



                        









                    }   };
                    
                    
                    $scope.switchTab=function()
                    {$("#resultTable").hide();
                     $("#favTable").hide();
                        if($scope.searchType=="places"){
                        $scope.fbData=$scope.placeData;
                        $scope.next=$scope.placeNext;
                        $scope.prev=$scope.placePrev;}
                        
                        if($scope.searchType=="events"){
                        $scope.fbData=$scope.eventData;
                        $scope.next=$scope.eventNext;
                        $scope.prev=$scope.eventPrev;}
                        if($scope.searchType=="pages"){
                        $scope.fbData=$scope.pageData;
                        $scope.next=$scope.pageNext;
                        $scope.prev=$scope.pagePrev;}
                         if($scope.searchType=="groups"){
                        $scope.fbData=$scope.groupData;
                        $scope.next=$scope.groupNext;
                        $scope.prev=$scope.groupPrev;}
                        if($scope.searchType=="users"){
                        $scope.fbData=$scope.userData;
                        $scope.next=$scope.userNext;
                        $scope.prev=$scope.userPrev;}
                    
                     if($scope.searchKey!="undefined"&&$scope.searchKey!=""&&$scope.searchKey!=null&&$scope.searchType!="favorites")
                        $("#resultTable").show("slow");
                    };

                    //method for Clearing the results
                    $scope.clearResults = function() {
                        $("#resultTable").hide("slow");
                        $("#favTable").hide("slow");
                        $("#detailPage").hide("slow");
                        $scope.searchKey = "";
                        $scope.searchType=null;
                        $scope.searchkey=null;
                        
                         $scope.next = $scope.prev = null;$scope.fbData=null;
                       $scope.placeData=$scope.eventData=$scope.userData=$scope.groupData=$scope.pageData=null; $scope.placeNext=$scope.placePrev=$scope.userPrev=$scope.userNext=$scope.groupNext=$scope.groupPrev=null;
                        $scope.pagePrev=$scope.pageNext=$scope.eventPrev=$scope.eventNext=null;
                        $("*").removeClass("active");
                        $("#user").addClass("active");
                    };


                    //favorite add remove
                    $scope.makeFav = function(id2, idname, idpic, idtype) {

                        localStorage.setItem(id2, idname + "," + idpic + "," + idtype);
                        $scope.localStrorage = localStorage;
                    };
                    //function to remove from favorites
                    $scope.removeFav = function(id2) {
                        localStorage.removeItem(id2);
                        $scope.localStrorage = localStorage;
                    };

                    //load next or previous data
                    $scope.loadNew = function(key) {
                        //alert("Load New Caled!");
                        $("#resultTable").css("display", "none");
                        $("#pgbar1").css("display", "block");
                        $http.get(key).then(function(response, $compile) {
                            $("#pgbar1").css("display", "none");
                            $scope.prev = null;
                            $scope.fbData = response.data.data;
                            if (response.data.paging != null) {
                                $scope.next = response.data.paging.next;
                                if (response.data.paging.previous != null)
                                    $scope.prev = response.data.paging.previous;



                            }

                            $("#resultTable").show('slow');

                        });

                    };

                    //LOADING FAVORITES TABLE
                    $scope.loadFavoritesTable = function() {
                      //  alert("Called");
                        $scope.favData=null;
                        $("#resultTable").hide("");
                       $("#favTable").hide();
                        $("#detailPage").hide("");
                        var arr = [];
                        for (var j = 0; j < localStorage.length; j++) {

                            idRow = localStorage.getItem(localStorage.key(j));
                            idRowDetail = idRow.split(",");
                            //console.log(localStorage.key(j)+"="+idRowDetail);
                            var itemName = idRowDetail[0];
                            var itempic = idRowDetail[1];
                            var itemType = idRowDetail[2];



                            obj1 = {
                                "id": localStorage.key(j),
                                "name": itemName,
                                "pic": itempic,
                                "type": itemType

                            };
                            arr.push(obj1);
                            $scope.favData = arr;
                            $("#favTable").show("slow");
                        }



                    };


                    //LOADING Details now..
                    $scope.loadDetails = function(id2, idname, idpic, idtype,incomingFromPage) {
                        $scope.detailId = id2;
                        $scope.detailPic = idpic;
                        $scope.detailName = idname;
                        $scope.profpic = idpic;
                        $scope.profname = idname;
                        $scope.fav = false;
                        $scope.localStorage = localStorage;
                        $scope.fromPage=incomingFromPage;
                       // alert(idtype);

                        if (localStorage.getItem(id2) == null) $scope.fav = false;
                        else $scope.fav = true;
                        //alert($scope.profpic);
                        $("#resultTable").hide();
                        $("#favTable").hide();;
                       $("#detailPage").hide();
                        $("#albumpgbar").show();
                        $("#postpgbar").show();  
                        
                        if(idtype!='events')
                       {
                        $http.get("http://gghw6-161921.appspot.com?id=" + id2)
                                               .then(function(response, $compile) {
                               // alert("hi");
                                $("#albumpgbar").hide();
                                $("#postpgbar").hide();
                               
                                $scope.resultData = response.data;
                               
                                
                                if ($scope.resultData.albums != null && $scope.resultData.albums.data.length != 0) {
                                    $scope.albums = $scope.resultData.albums.data;
                                    
                                }
                                if ($scope.resultData.posts != null && $scope.resultData.posts.data.length != 0) {
                                    $scope.postsinitial = $scope.resultData.posts.data;
                                    for (var posti = 0; posti < $scope.postsinitial.length; posti++) {
                                        $scope.postsinitial[posti].created_time = moment($scope.postsinitial[posti].created_time).format('YYYY-MM-DD HH:mm:ss');


                                    }
                                    $scope.posts = $scope.postsinitial;

                                }


                                //alert($scope.resultData.data.albums.data[0].photos.data[0].name);
                                

                                   
                                $("#detailPage").show("slow");
                                $("#resultTable").hide();


                            });
                    
                    }
                    else{
                         $("#albumpgbar").hide();
                                $("#postpgbar").hide();
                        $scope.posts=$scope.albums=null; $("#detailPage").show();
                    }
                        
                        
                    };



                    $scope.navigateTo=function(){
                        
                       
                        if($scope.fromPage=="favorite")
                                                {
                                                    $("#detailPage").hide("slow"); $("#resultTable").hide();$scope.loadFavoritesTable();
                                                }
                                                 else
                                                {$("#detailPage").hide(); $("#resultTable").show("slow"); }
                                                };



                    $scope.postFB = function() {



                        FB.ui({
                            app_id: '1691655584460409',
                            method: 'feed',
                            link: window.location.href,
                            picture: $scope.detailPic,
                            name: $scope.detailName,
                            caption: 'FB SEARCH FROM USC CSCI571',
                        }, function(response) {
                            if (response && !response.error_message)
                                alert("Posted Successfully");
                            else
                                alert("Not Posted");
                        });




                    };




                }
            ]


        );


        //Method to disappear image toggle

        function disappearImage(this2) {


            if ($(this2).next().css("display") == "block") {
                $("[id$='Image']").slideUp(1000);
            }
            if ($(this2).next().css("display") == "none") {
                $("[id$='Image']").slideUp(800);
                $(this2).next().removeClass("ng-hide");
                $(this2).next().slideDown(1000);


            }


        }

        function checkOutFav(this2) {
            // alert($(this2).children("i").attr("class"));
            if ($(this2).children("i").attr("class") == "glyphicon glyphicon-star")
                $(this2).children("i").removeClass("glyphicon-star").addClass("glyphicon-star-empty").css("color", "");
            else
                $(this2).children("i").removeClass("glyphicon-star-empty").addClass("glyphicon-star").css("color", "yellow");

        }
