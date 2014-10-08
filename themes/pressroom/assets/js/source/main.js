$checkImage = $('.cover__image');
$checkTitle = $('.cover__title--resize');
$checkEmbed = $('.entry-content-asset');

document.addEventListener('DOMContentLoaded', function () {
	if ($checkImage.length > 0){
		BackgroundCheck.init({
			targets: '.check',
			images: '.cover__image',
			windowEvents: false
		});
		var s = skrollr.init();
	}
});

if ($checkTitle.length > 0){
	textFit(document.getElementsByClassName('cover__title--resize')[0], {minFontSize:10, maxFontSize: 150});
}

if ($checkEmbed.length > 0){
	$(".entry-content-asset").fitVids();
}

var ua = window.navigator.userAgent;

var str = "BakerFramework";
var n = ua.search(str);

console.log(n);

if (n < 0) {
  $( "<style>@font-face{font-family:'Alt Ren';src:url('fonts/alt_ren_retro.eot');src:url('fonts/alt_ren_retro.eot?#iefix')format('embedded-opentype'),url('fonts/alt_ren_retro.woff')format('woff'),url('fonts/alt_ren_retro.ttf')format('truetype'),url('fonts/alt_ren_retro.svg#a2473ca7d801e7d1ecff4072e5f90ab4')format('svg');font-style:normal;font-weight:400;}@font-face{ font-family:'Bodoni Std Poster';src:url('fonts/BodoniStd-Poster.eot');src:url('fonts/BodoniStd-Poster.eot?#iefix')format('embedded-opentype'),url('fonts/BodoniStd-Poster.woff')format('woff'),url('fonts/BodoniStd-Poster.ttf')format('truetype'),url('fonts/BodoniStd-Poster.svg#98653a9b817332e12a752607aaac382f')format('svg');font-style:normal;font-weight:400;}@font-face{ font-family:'Figa';src:url('fonts/Figa.eot');src:url('fonts/Figa.eot?#iefix')format('embedded-opentype'),url('fonts/Figa.woff')format('woff'),url('fonts/Figa.ttf')format('truetype'),url('fonts/Figa.svg#79d545aa02eb0aa08e4f30160341891b')format('svg');font-style:normal;font-weight:400;}@font-face{ font-family:'Gotham Thin';src:url('fonts/Gotham-Thin.eot');src:url('fonts/Gotham-Thin.eot?#iefix')format('embedded-opentype'),url('fonts/Gotham-Thin.woff')format('woff'),url('fonts/Gotham-Thin.ttf')format('truetype'),url('fonts/Gotham-Thin.svg#2612f69d108fca39cb109c03ae310193')format('svg');font-style:normal;font-weight:200;}@font-face{ font-family:'Metropolis 1920';src:url('fonts/Metropolis 1920.eot');src:url('fonts/Metropolis 1920.eot?#iefix')format('embedded-opentype'),url('fonts/Metropolis 1920.woff')format('woff'),url('fonts/Metropolis 1920.ttf')format('truetype'),url('fonts/Metropolis 1920.svg#5af72d8780e89b72e0db0e90eaa50bfd')format('svg');font-style:normal;font-weight:400;}@font-face{ font-family:'Minion Web Pro';src:url('fonts/MinionWebPro.eot');src:url('fonts/MinionWebPro.eot?#iefix')format('embedded-opentype'),url('fonts/MinionWebPro.woff')format('woff'),url('fonts/MinionWebPro.ttf')format('truetype'),url('fonts/MinionWebPro.svg#37c2ad9bfb4ef36c76926319d6db291e')format('svg');font-style:normal;font-weight:400;}@font-face{ font-family:'Minion Web Pro';src:url('fonts/MinionWebPro-Bold.eot');src:url('fonts/MinionWebPro-Bold.eot?#iefix')format('embedded-opentype'),url('fonts/MinionWebPro-Bold.woff')format('woff'),url('fonts/MinionWebPro-Bold.ttf')format('truetype'),url('fonts/MinionWebPro-Bold.svg#c794e83c759d2912b8fc0cfb91a35590')format('svg');font-style:normal;font-weight:700;}@font-face{ font-family:'Minion Web Pro';src:url('fonts/MinionWebPro-Italic.eot');src:url('fonts/MinionWebPro-Italic.eot?#iefix')format('embedded-opentype'),url('fonts/MinionWebPro-Italic.woff')format('woff'),url('fonts/MinionWebPro-Italic.ttf')format('truetype'),url('fonts/MinionWebPro-Italic.svg#77454d232c071012ee5f73fdf227ce29')format('svg');font-style:italic;font-weight:400;}@font-face{ font-family:'Proxima Nova Bl';src:url('fonts/ProximaNova-Black.eot');src:url('fonts/ProximaNova-Black.eot?#iefix')format('embedded-opentype'),url('fonts/ProximaNova-Black.woff')format('woff'),url('fonts/ProximaNova-Black.ttf')format('truetype'),url('fonts/ProximaNova-Black.svg#5216c1cacb478b35a513d81c80dc8ad7')format('svg');font-style:normal;font-weight:400;}@font-face{ font-family:'Proxima Nova Rg';src:url('fonts/ProximaNova-Bold.eot');src:url('fonts/ProximaNova-Bold.eot?#iefix')format('embedded-opentype'),url('fonts/ProximaNova-Bold.woff')format('woff'),url('fonts/ProximaNova-Bold.ttf')format('truetype'),url('fonts/ProximaNova-Bold.svg#d1e00a8fb1fd9b428ab6b90409479fde')format('svg');font-style:normal;font-weight:700;}@font-face{ font-family:'Proxima Nova Rg';src:url('fonts/ProximaNova-RegItalic.eot');src:url('fonts/ProximaNova-RegItalic.eot?#iefix')format('embedded-opentype'),url('fonts/ProximaNova-RegItalic.woff')format('woff'),url('fonts/ProximaNova-RegItalic.ttf')format('truetype'),url('fonts/ProximaNova-RegItalic.svg#77f1c682604b6b0472d490a8e6c22d18')format('svg');font-style:italic;font-weight:400;}@font-face{ font-family:'Proxima Nova Rg';src:url('fonts/ProximaNova-Regular.eot');src:url('fonts/ProximaNova-Regular.eot?#iefix')format('embedded-opentype'),url('fonts/ProximaNova-Regular.woff')format('woff'),url('fonts/ProximaNova-Regular.ttf')format('truetype'),url('fonts/ProximaNova-Regular.svg#2c1459d58560f08b4b0586fbe80aa4cd')format('svg');font-style:normal;font-weight:400;}@font-face{ font-family:'Sanotra';src:url('fonts/Sanotra Regular.eot');src:url('fonts/Sanotra Regular.eot?#iefix')format('embedded-opentype'),url('fonts/Sanotra Regular.woff')format('woff'),url('fonts/Sanotra Regular.ttf')format('truetype'),url('fonts/Sanotra Regular.svg#0acdb647768a7b07cc1a92d5c124ab37')format('svg');font-style:normal;font-weight:400;}</style>").appendTo( "head" );
}


