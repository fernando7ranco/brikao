$(function () {

    var googleUser = {};

    var startApp = function () {
        gapi.load('auth2', function () {
            auth2 = gapi.auth2.init({
                client_id: '843116202415-hi04h3fuv8f1prhcdtrs9r9vcqc9p0mh.apps.googleusercontent.com',
                cookiepolicy: 'single_host_origin',

            });
            attachSignin(document.getElementById('googleApp'));
        });
    };

    function attachSignin(element) {
        auth2.attachClickHandler(element, {},
			function (googleUser) {
				imgLoad(true);
				var reponse = googleUser.getBasicProfile();

				if (JSON.stringify(reponse) !== '{}') {

					var dados = {
						email: reponse.getEmail(),
						name: reponse.getName(),
						id: reponse.getId(),
						tipo: 3
					};

					functionApps(dados, imgLoad);
				} else {
					alert('erro no sistema');
					imgLoad(false);
				}
			}, function (error) {
				//alert(JSON.stringify(error, undefined, 2));
				imgLoad(false);
			}
		);
    };

    function imgLoad(qual) {
        if (qual){
            if( $('#googleApp').find('#loadApp').length == 0)
                $('#googleApp').append("<img src='../assets/img/icones/loading.gif' id='loadApp' >");
        }else
            $('#googleApp').find("#loadApp").remove();

    };

    startApp();

});