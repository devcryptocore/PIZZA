document.addEventListener('DOMContentLoaded',()=>{

    const soption = document.querySelectorAll(".source-option");
    const iframe = document.querySelector("#sites_container");
    soption.forEach(b => {
        b.addEventListener('click',()=>{
            let source = b.getAttribute("data-source");
            source = `../admin/${source}.php`;
            if(source == "logout"){
                location.href = "../php/logout.php";
            }
            if(b.getAttribute("data-source") == 'acercade'){
                source = 'https://devcryptocore.github.io';
            }
            iframe.setAttribute('src',source);
        });      
    });

    

});