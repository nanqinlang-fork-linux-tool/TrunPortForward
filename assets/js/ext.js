function setCookie(c_name,value,expiredays)
{
    var exdate=new Date()
    exdate.setDate(exdate.getDate()+expiredays)
    document.cookie=c_name+ "=" +escape(value)+
        ((expiredays==null) ? "" : ";expires="+exdate.toGMTString())+";path=/"
}

function changeLanguage(lang) {
    setCookie('think_language',null);
    setCookie('think_language',lang);
    location.reload()
}

function goBack() {
    if (document.referrer!="") {
        window.location = document.referrer;
    } else {
        window.location = '/';
    }
}