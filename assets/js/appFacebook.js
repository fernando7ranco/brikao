$(function () {
	
	(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id))
            return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
	
    window.fbAsyncInit = function() {
		FB.init({
		  appId      : '318839765181943',
		  xfbml      : true,
		  version    : 'v2.8'
		});
		FB.AppEvents.logPageView();
	};

    $('#facebookApp').click(function () {
        imgLoad(true);
        statusChange();
    });

    function statusChange() {

        FB.getLoginStatus(function (response) {
			
            if (response.status === 'connected')
                FB.api('/me?fields=name,email', function (response) {
					dados = response;
					dados['tipo'] = 2;
					functionApps(dados, imgLoad);
				});
            else if (response.status === 'not_authorized') {
                alert('autorização negada');
                imgLoad(false);
            } else
                boxLogin();
        });
		
    };


    function boxLogin() {

        FB.login(function (response) {

            if (response.authResponse)
                statusChange();
            else {
                alert('autorização negada');
                imgLoad(false);
            }

        }, {scope: 'email', auth_type: 'rerequest'});

    };

    function imgLoad(qual) {

        if (qual) {
            if ($('#facebookApp').find('#loadApp').length == 0)
                $('#facebookApp').append("<img src='../assets/img/icones/loading.gif' id='loadApp' >");
        } else
            $('#facebookApp').find("#loadApp").remove();

    };

});