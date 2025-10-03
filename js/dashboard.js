document.addEventListener('DOMContentLoaded',()=>{

    const soption = document.querySelectorAll(".source-option");
    const iframe = document.querySelector("#sites_container");
    soption.forEach(b => {
        b.addEventListener('click',()=>{
            let source = b.getAttribute("data-source");
            if(source == "logout"){
                location.href = "../php/logout.php";
            }
            iframe.setAttribute('src',`../admin/${source}.php`);
        });      
    });

});