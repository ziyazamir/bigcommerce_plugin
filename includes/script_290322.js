$(document).ready(function() {
    function parseJwt(token) {
        var base64Url = token.split(".")[1];

        var base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");

        var jsonPayload = decodeURIComponent(
            atob(base64)
            .split("")

            .map(function(c) {
                return "%" + ("00" + c.charCodeAt(0).toString(16)).slice(-2);
            })

            .join("")
        );

        let temp = JSON.parse(jsonPayload);

        let id = temp.customer.id;

        return id;
    }

    function hide_designo_data() {
        let key = $(".definitionList-key");
        // console.log(key);
        $(key).each(function() {
            let tar = this;
            //   $(tar).addClass("disp");
            //   $(tar).next().addClass("disp");
            // console.log($(tar).text());
            if ($(tar).text().indexOf("designo-data") >= 0) {
                $(tar).next().hide();
                $(tar).hide();
            } else if ($(tar).text().indexOf("designo-image") >= 0) {
                let img = $(tar).next();
                let img_link = img.text();
                if (img_link.indexOf("designo-image") >= 0) {
                    //   console.log(img_link);
                    //   img.empty();
                    //   let link =
                    //     '<a href="' + img_link + '" target="_blank">custom image</a>';
                    //   // img.append(link);
                    //   img.html(
                    //     '<a href="' + img_link + '" target="_blank">custom image</a>'
                    //   );
                    $(img).hide();
                    $(tar).hide();
                    // img.html;
                } else {
                    console.log(img_link);
                    img.empty();
                    let link =
                        '<a href="' + img_link + '" target="_blank">custom image</a>';
                    // img.append(link);
                    img.html(
                        '<a href="' + img_link + '" target="_blank">custom image</a>'
                    );
                }
                const anchor = [...this.parentElement.parentElement.children].find(
                    (elem) => elem.hasAttribute("data-item-edit")
                );
                if (anchor) anchor.style.display = "none";
            } else if ($(tar).text().indexOf("designo-price") >= 0) {
                $(tar).next().hide();
                $(tar).hide();
            }
        });
    }
    var head = document.getElementsByTagName("HEAD")[0];

    // Create new link Element
    var link = document.createElement("link");

    // set the attributes for link element
    link.rel = "stylesheet";

    link.type = "text/css";

    link.href =
        "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css";

    // Append link element to HTML head
    head.appendChild(link);
    // alert(bigcommerce_config.cart.getCartID());
    let shop_name = $(location).attr("hostname");
    localStorage.setItem("big_shop", shop_name);
    let check;
    const app_domain = "bigcommerceapp.designo.software/v1_ppk";
    let yes = "yes";
    let designo_link;
    let page = window.location.pathname;
    let product_page = $("input[value='add']").val();
    $.ajax({
        url: "https://" + app_domain + "/my-api/state.php",
        type: "POST",
        // dataType: "json",
        data: {
            store: shop_name,
        },
        success: function(res) {
            console.log(res);
            let arr = res.split("&");
            console.log(arr);
            check = arr[0];
            console.log(check);
            designo_link = arr[1];
            if (check == yes) {
                // alert("it is enabled");

                let c_id;

                let form_url = "https://" + shop_name + "/cart.php";
                $("form[action='" + form_url + "']").append(
                    '<button id="my-loader" style="display: block;float: none;clear: both;" class="button button--primary" type="button"><i style="margin-right:10px" class="fa fa-spinner fa-spin"></i>Loading</button>'
                );
                //   alert(page);

                // console.log(url);

                //   alert(store_name);

                console.log(shop_name);

                console.log(product_page);

                if (product_page != undefined) {
                    let designo_input = $("input[type='text']");
                    // console.log(designo_input);
                    $(designo_input).each(function() {
                        // let input = this;
                        // console.log($(input).val());
                        let val = $(this).val();
                        if (val.indexOf("designo-data") >= 0) {
                            $(this).parent().hide();
                        } else if (val.indexOf("designo-image") >= 0) {
                            $(this).parent().hide();
                        } else if (val.indexOf("designo-price") >= 0) {
                            $(this).parent().hide();
                        }
                    });
                    // alert("a product page");
                    let form_data;
                    let product_id = $("input[name='product_id']").val();
                    let product_name = $(location).attr("pathname").replace(/\//g, "");
                    console.log(product_id);

                    $.ajax({
                        url: designo_link + "api/studio/ecomm-token",

                        type: "POST",

                        success: function(data) {
                            let etoken = data.token;

                            //console.log(etoken);

                            $.ajax({
                                url: designo_link + "api/studio/is-product-customized",

                                type: "POST",

                                data: {
                                    id: product_id,

                                    SKU: product_id,

                                    store_id: shop_name,
                                },

                                headers: {
                                    Authorization: etoken,
                                },

                                success: function(data) {
                                    console.log(data);

                                    console.log(data["url"]);

                                    ifr_url = data["url"];

                                    console.log(ifr_url);

                                    if (typeof data["url"] === "undefined") {
                                        $("#my-loader").hide();
                                        // alert("undefined");
                                    } else {
                                        $("#my-loader").hide();

                                        $("form[action='" + form_url + "']").append(
                                            '<button style="margin:0;display: block;float: none;clear: both;" id="customize" class="button button--primary" type="button">Customize</button>'
                                        );
                                    }
                                },

                                error: function(xhr, ajaxOptions, thrownError) {
                                    $("#my-loader").hide();
                                    console.log(ajaxOptions);

                                    console.log(xhr.status);

                                    console.log(thrownError);
                                },
                            });
                        },
                    });

                    $(document).on("click", "#customize", function() {
                        form_data = $("form[action='" + form_url + "']").serializeArray();
                        //console.log(form_data);
                        function toObject(arr) {
                            var rv = {};
                            for (var i = 0; i < arr.length; ++i) {
                                if (
                                    arr[i].value == "add" ||
                                    arr[i].value == "designo-image" ||
                                    arr[i].value == "designo-data"
                                ) {
                                    continue;
                                } else {
                                    rv[arr[i].name] = arr[i].value;
                                }
                            }
                            return rv;
                        }
                        console.log(JSON.stringify(toObject(form_data)));
                        window.location.href =
                            "/designo?pr_id=" + JSON.stringify(toObject(form_data));
                    });
                }

                if (page.indexOf("designo") >= 0) {
                    let cart_p;
                    let cart_id;
                    // let cart_id;
                    let searchParams = new URLSearchParams(window.location.search);

                    let pr_details = searchParams.get("pr_id");
                    let product_name = searchParams.get("pr_name");
                    // cart[0].id;
                    // alert("hello");
                    function get_cart_id() {
                        var settings = {
                            async: true,

                            crossDomain: true,

                            url: "https://" + shop_name + "/api/storefront/carts",

                            method: "GET",

                            headers: {},
                        };

                        $.ajax(settings).done(function(response) {
                            console.log(response);

                            cart = response;
                            cart_p = jQuery.isEmptyObject(response);
                            // alert(cart_p);
                            console.log("cart" + cart_p);
                            cart_id = cart[0].id;
                        });
                    }
                    get_cart_id();

                    // alert("designo");

                    // debugger;

                    $("header").hide();

                    $("footer").hide();

                    $("body").empty();

                    $("body").append(
                        "<iframe id='designtool_iframe' type='text/html' target='_parent' name='Design N Buy' style='display: none;' frameborder='0' scrolling='yes' allowfullscreen=' width='100%' height='100%'></iframe>"
                    );

                    // $("body").append("<div id='#cust'> {{customer.id}}</div>");

                    // alert(product_name);
                    console.log(pr_details);

                    let obj1 = JSON.parse(pr_details);
                    console.log(obj1.product_id);
                    const entries = Object.entries(obj1);
                    let verify = {};
                    verify["id"] = obj1.product_id;
                    verify["SKU"] = obj1.product_id;
                    verify["store_id"] = shop_name;
                    verify["super_attribute"] = {};
                    verify["options"] = {};
                    // $(pr_options).each(function () {
                    //   verify["super_attribute"][this] = variant_id;
                    //   // verify["super_attribute"].push(temp);
                    // });
                    // console.log(verify);
                    // console.log(entries[0][0]);
                    let options = [
                        [1, 2],
                        [3, 4],
                        //   [5, 6],
                    ];
                    let y = 0;
                    $(entries).each(function() {
                        //   console.log(this[0]);
                        let str = this[0];
                        let x = 0;
                        //   console.log(str);
                        if (str.indexOf("attribute") >= 0) {
                            // var txt = "#div-name-1234-characteristic:561613213213";
                            var numb = str.match(/\d/g);
                            numb = numb.join("");
                            // alert (numb);​
                            let value = this[1];
                            verify["super_attribute"][numb] = value;
                            verify["options"][numb] = value;
                            // options[y][x] = numb;
                            // options[y][x + 1] = value;
                            y++;
                            // console.log(numb);
                            // console.log(value);
                        }
                    });
                    console.log(verify);
                    // var result = Object.entries(obj);

                    // console.log(result);
                    // Iterate over data
                    // alert(product_id);

                    $.ajax({
                        url: designo_link + "api/studio/ecomm-token",

                        type: "POST",

                        success: function(data) {
                            let etoken = data.token;

                            console.log(etoken);
                            // verify["super_attribute"].push(temp);
                            console.log("verify:" + verify);

                            $.ajax({
                                url: designo_link + "api/studio/is-product-customized",

                                type: "POST",

                                data: verify,

                                headers: {
                                    Authorization: etoken,
                                },

                                success: function(data) {
                                    console.log(data);

                                    console.log(data["url"]);

                                    ifr_url = data["url"];

                                    console.log(ifr_url);

                                    $("#designtool_iframe").attr("src", ifr_url);

                                    if (typeof data["url"] === "undefined") {
                                        // $("#my-loader").hide();
                                        // alert("undefined");
                                    } else {
                                        // $("#my-loader").hide();

                                        // $("#designtool_iframe").css({

                                        //   position: "fixed",

                                        //   top: "0",

                                        //   left: "0",

                                        // });

                                        $("#designtool_iframe").width("100vw");

                                        $("#designtool_iframe").height("100vh");

                                        $("#designtool_iframe").position("fixed");

                                        $("#designtool_iframe").show();

                                        // alert($("#cust").text());
                                    }
                                },

                                error: function(xhr, ajaxOptions, thrownError) {
                                    console.log(ajaxOptions);

                                    console.log(xhr.status);

                                    console.log(thrownError);
                                },
                            });
                        },
                    });

                    var w2pDomain = designo_link;

                    window.addEventListener("message", async function(event) {
                        //   var obj = {
                        //     val: c_id,

                        //     shop: shop_name,
                        //   };

                        // alert('hiiiii');

                        if (event.origin + "/" !== w2pDomain || !event.data.action) return;

                        if (event.data.action === "add_cart") {
                            var designo_formData = event.data.cartData;
                            var _field = JSON.stringify(designo_formData);
                            console.log(designo_formData);
                            // let field_form = $("form[name='customOptionForm']");
                            // console.log(field_form);
                            png_img = designo_link + "images/cart/" + designo_formData.png;
                            let object = {};
                            object.pr_id = designo_formData.product;
                            object.cartId = cart_id;
                            //   this.alert(cart_id);
                            object.shop = shop_name;
                            // object.img = designo_formData.png;
                            //object.price = designo_formData.addon_price;
                            object.price = designo_formData.total_price;
                            object.quantity = designo_formData.qty;
                            object.options = {};
                            if (designo_formData.color_id != null) {
                                object.options[designo_formData.color_id] =
                                    designo_formData[
                                        "super_attribute[" + designo_formData.color_id + "]"
                                    ];
                                // id = formdata["super_attribute[" + formdata.color_id + "]"];
                                // console.log("it is id" + id);
                            }
                            if (designo_formData.size_id != null) {
                                // this.alert("size");
                                object.options[designo_formData.size_id] =
                                    designo_formData[
                                        "super_attribute[" + designo_formData.size_id + "]"
                                    ];
                                // id = formdata["super_attribute[" + formdata.size_id + "]"];
                            }
                            console.log(typeof designo_formData.customOptionData);
                            let customoptions = JSON.parse(designo_formData.customOptionData);
                            console.log(customoptions);
                            $.each(customoptions, function(key, value) {
                                // alert(key + ": " + value);
                                let option_id = key.match(/\d+/)[0];
                                if (key.indexOf("designo-data") >= 0) {
                                    object.options[option_id] = _field;
                                } else if (key.indexOf("designo-image") >= 0) {
                                    object.options[option_id] = png_img;
                                } else {
                                    object.options[option_id] =
                                        designo_formData["options[" + option_id + "]"];
                                }
                                console.log(key.match(/\d+/)[0] + key);
                                // let tag = "<option>" + value + "</option>";
                                // $("#scat").append(tag);
                            });
                            //   object.options["113"] = 98;
                            //   object.options["114"] = 101;
                            //   object.options["125"] = _field;
                            //   object.options["127"] = png_img;
                            //   object.options["130"] = designo_formData["options[130]"];
                            console.log(object);

                            let json_data = JSON.stringify(object);
                            console.log(json_data);
                            fetch(
                                    "https://" + app_domain + "/my-api/create_cart.php",

                                    {
                                        method: "POST",

                                        headers: {
                                            Accept: "application/json",

                                            "Content-Type": "application/json",
                                        },

                                        //make sure to serialize your JSON body

                                        body: JSON.stringify(object),
                                    }
                                )
                                .then((res) => res.json())
                                .then((res) => {
                                    console.log(res);
                                    let url = JSON.stringify(res);
                                    console.log(url);
                                    // alert(url);
                                    window.location = res;
                                });
                        } else if (event.data.action === "login_check") {
                            // alert("check login");

                            function customerJWT() {
                                var appClientId = "prfjlcew9txt2u5vhfs3jrqgg57ve57"; // TODO: Fill this in with your app's client ID

                                var xmlhttp = new XMLHttpRequest();

                                xmlhttp.onreadystatechange = function() {
                                    if (xmlhttp.readyState == 4) {
                                        if (xmlhttp.status == 200) {
                                            // alert("Customer JWT:\n" + xmlhttp.responseText);

                                            c_id = parseJwt(xmlhttp.responseText);
                                            localStorage.setItem("big_user", c_id);

                                            var obj = {
                                                val: c_id,

                                                shop: shop_name,
                                            };
                                            console.log(obj);
                                            fetch(
                                                    "https://" + app_domain + "/my-api/logincheck.php", {
                                                        method: "POST",

                                                        headers: {
                                                            Accept: "application/json",

                                                            "Content-Type": "application/json",
                                                        },

                                                        //make sure to serialize your JSON body

                                                        body: JSON.stringify(obj),
                                                    }
                                                )
                                                .then((res) => res.json())

                                            .then((res) => {
                                                console.log(typeof res);

                                                event.source.postMessage({
                                                        res,

                                                        action: "login_check",
                                                    },

                                                    event.origin
                                                );
                                            })

                                            .catch((error) => {
                                                console.log("logincheck error", error);
                                            });
                                            console.log(c_id);
                                        } else if (xmlhttp.status == 404) {
                                            let error;
                                            localStorage.setItem("big_user", "not logged in");
                                            // error["df"] = "sdf";
                                            /*error["data"] = {};
                                                                                                              error["data"]["success"] = "false";
                                                                                                              error["error"] = {};
                                                                                                              error["error"]["message"] = "user not logged in";
                                                                                                              var my_error=JSON.stringify(error);
                                                                                                              console.log(error);
                                                                                                              console.log(typeof my_error);*/
                                            error =
                                                '{"data": { "success": "false" }, "error": { "message": "user not logged in" }}';
                                            console.log(typeof error);
                                            //error=JSON.parse(error);
                                            event.source.postMessage({
                                                    res: JSON.parse(error),

                                                    action: "login_check",
                                                },

                                                event.origin
                                            );
                                        } else {
                                            alert("Something went wrong");
                                        }
                                    }
                                };

                                xmlhttp.open(
                                    "GET",

                                    "/customer/current.jwt?app_client_id=" + appClientId,

                                    true
                                );

                                xmlhttp.send();

                                // return c_id;
                            }

                            console.log(customerJWT());
                        } else if (event.data.action === "login") {
                            fetch(
                                    "https://magento.designo.software/en/w2p/customer/loginfromtool",

                                    {
                                        method: "POST",

                                        headers: {
                                            "Content-Type": "application/x-www-form-urlencoded",
                                        },

                                        body: ObjectToURLParams(event.data.data),
                                    }
                                )
                                .then((res) => res.json())

                            .then((res) => {
                                event.source.postMessage({
                                        res,

                                        action: "login",
                                    },

                                    event.origin
                                );
                            })

                            .catch((error) => {
                                console.log("login from tool error", error);
                            });
                        } else if (event.data.action === "logout") {
                            fetch(
                                    "https://magento.designo.software/en/w2p/customer/logoutfromtool",

                                    {
                                        method: "POST",

                                        headers: {
                                            "Content-Type": "application/x-www-form-urlencoded",
                                        },
                                    }
                                )
                                .then((res) => res.json())

                            .then((res) => {
                                event.source.postMessage({
                                        res,

                                        action: "logout",
                                    },

                                    event.origin
                                );
                            })

                            .catch((error) => {
                                console.log("logout from tool error", error);
                            });
                        } else if (event.data.action === "register") {
                            fetch(
                                    "https://magento.designo.software/en/w2p/customer/createfromtool",

                                    {
                                        method: "POST",

                                        headers: {
                                            "Content-Type": "application/x-www-form-urlencoded",
                                        },

                                        body: ObjectToURLParams(event.data.data),
                                    }
                                )
                                .then((res) => res.json())

                            .then((res) => {
                                event.source.postMessage({
                                        res,

                                        action: "register",
                                    },

                                    event.origin
                                );
                            })

                            .catch((error) => {});
                        } else if (event.data.action === "user_details") {
                            var cust_obj = {
                                val: this.localStorage.getItem("big_user"),

                                shop: this.localStorage.getItem("big_shop"),
                            };
                            fetch(
                                    "https://" + app_domain + "/my-api/customerdetails.php",

                                    {
                                        method: "POST",

                                        headers: {
                                            Accept: "application/json",

                                            "Content-Type": "application/json",
                                        },

                                        //make sure to serialize your JSON body

                                        body: JSON.stringify(cust_obj),
                                    }
                                )
                                .then((res) => res.json())

                            .then((res) => {
                                event.source.postMessage({
                                        res,

                                        action: "user_details",
                                    },

                                    event.origin
                                );
                            })

                            .catch((error) => {
                                console.log("User details from tool error", error);
                            });
                        } else if (event.data.action === "back") {
                            window.location.href = document.referrer;
                        } else if (event.data.action === "home") {
                            window.location.href = "/";
                        } else if (event.data.action === "mydesign") {
                            window.open(event.data.url, "_blank");
                        }
                    });
                }

                if (page.indexOf("account") >= 0) {
                    // alert("account page");
                    hide_designo_data();

                    function customerJWT() {
                        var appClientId = "prfjlcew9txt2u5vhfs3jrqgg57ve57"; // TODO: Fill this in with your app's client ID

                        var xmlhttp = new XMLHttpRequest();
                        xmlhttp.onreadystatechange = function() {
                            if (xmlhttp.readyState == 4) {
                                if (xmlhttp.status == 200) {
                                    // alert("Customer JWT:\n" + xmlhttp.responseText);
                                    loggedUser = parseJwt(xmlhttp.responseText);
                                    // alert(loggedUser);
                                    let etoken1 = "";
                                    $.ajax({
                                        url: designo_link + "api/studio/ecomm-token",

                                        type: "POST",

                                        success: function(data) {
                                            // console.log(data.token);

                                            etoken1 = data.token;

                                            // console.log(etoken1);
                                            $.ajax({
                                                url: designo_link +
                                                    "app/designs/my-designs/" +
                                                    loggedUser +
                                                    "/" +
                                                    shop_name +
                                                    "/" +
                                                    etoken1,

                                                type: "GET",
                                                success: function(data) {
                                                    // console.log(data);
                                                },
                                            });
                                        },
                                    });
                                    // acount page script
                                    var par = $('a[href="/account.php?action=inbox"]')
                                        .parent()
                                        .parent();
                                    console.log(par);
                                    // par.last().after().empty;
                                    let ifra =
                                        "<iframe src='' name='myiframe' title='' allowfullscreen='true' style=' width: 100%; height: 400px;display:none;'></iframe>";
                                    par.last().after(ifra);
                                    par
                                        .last()
                                        .append(
                                            "<span id='designo_design' style='margin-left:8px;cursor: pointer;'>My Designs</span>"
                                        );
                                    par
                                        .last()
                                        .append(
                                            "<span id='designo_message' style='margin-left:8px;cursor: pointer;'>My Messages</span>"
                                        );
                                    $(document).on("click", "#designo_design", function() {
                                        let design_url =
                                            designo_link +
                                            "app/designs/my-designs/" +
                                            loggedUser +
                                            "/" +
                                            shop_name +
                                            "/" +
                                            etoken1;
                                        $("iframe[name='myiframe'").attr("src", design_url);
                                        $("iframe[name='myiframe'").slideDown();
                                    });
                                    $(document).on("click", "#designo_message", function() {
                                        let message_url =
                                            designo_link +
                                            "app/messages/my-messages/" +
                                            loggedUser +
                                            "/" +
                                            etoken1;
                                        $("iframe[name='myiframe'").attr("src", message_url);
                                        $("iframe[name='myiframe'").slideDown();
                                    });
                                } else if (xmlhttp.status == 404) {
                                    // alert("Not logged in!");
                                    let error = {};
                                    // error["df"] = "sdf";
                                    error["data"] = {};
                                    error["data"]["success"] = "false";
                                    error["error"] = {};
                                    error["error"]["message"] = "user not logged in";
                                    console.log(typeof error);
                                    event.source.postMessage({
                                            res: error,

                                            action: "login_check",
                                        },

                                        event.origin
                                    );
                                } else {
                                    alert("Something went wrong");
                                }
                            }
                        };

                        xmlhttp.open(
                            "GET",

                            "/customer/current.jwt?app_client_id=" + appClientId,

                            true
                        );

                        xmlhttp.send();

                        // return c_id;
                    }

                    console.log(customerJWT());
                }

                if (page.indexOf("checkout") >= 0) {
                    hideOptionsOnCheckout();
                    let counter = 0;
                    const inter = setInterval(() => {
                        counter++;
                        let temp = $("[data-test='cart-item-product-option']");
                        if (temp.length || counter > 5) {
                            hideOptionsOnCheckout();
                            clearInterval(inter);
                        }
                    }, 1000);
                }
            } else {
                console.warn("disabled");
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            console.log(ajaxOptions);
            console.warn(xhr.status);
            console.warn(thrownError);
        },
    });

    // create cart
    if (page.indexOf("cart.php") >= 0) {
        $("<style>.disp { display: none; }</style>").appendTo("head");
        hide_designo_data();
        // var intervalId = window.setInterval(function () {
        /// call your function here
        // }, 500);

        // alert("working");

        $(".swal2-confirm button").click(function() {
            setTimeout(function() {
                location.reload();
                // $("#signInButton").trigger("click");
            }, 1000);
        });
        const observerOptions = {
            childList: true,
        };
        const observer = new MutationObserver(function() {
            hide_designo_data();
            observer.disconnect();
            observer.observe(
                document.querySelector("[data-cart-content]"),
                observerOptions
            );
        });
        observer.observe(
            document.querySelector("[data-cart-content]"),
            observerOptions
        );
    }

    if (product_page != undefined) {
        let designo_input = $("input[type='text']");
        // console.log(designo_input);
        $(designo_input).each(function() {
            // let input = this;
            // console.log($(input).val());
            let val = $(this).val();

            if (val.indexOf("designo-data") >= 0) {
                $(this).parent().hide();
            } else if (val.indexOf("designo-image") >= 0) {
                $(this).parent().hide();
            } else if (val.indexOf("designo-price") >= 0) {
                $(this).parent().hide();
            }
        });
    } else {
        let designo_input = $("input[type='text']").parent();
        // console.log(designo_input);
        $(designo_input).each(function() {
            // let input = this;
            // console.log($(input).val());
            let vall = $(this).html();
            // alert(vall);

            if (val.indexOf("designo-data") >= 0) {
                $(this).parent().hide();
            } else if (val.indexOf("designo-image") >= 0) {
                $(this).parent().hide();
            } else if (val.indexOf("designo-price") >= 0) {
                $(this).parent().hide();
            }
        });
    }
    //   $("body").append("<div id='#cust'> {{customer.id}} </div>");
    // alert({{settings.store_hash}});

    function hideOptionsOnCheckout() {
        let temp = $("[data-test='cart-item-product-option']");
        if (temp.length) {
            $(temp).each(function() {
                if (
                    this.textContent.includes("designo-data") ||
                    this.textContent.includes("designo-image")
                ) {
                    this.style.display = "none";
                }
            });
        }
    }
});