document.querySelector('.input').addEventListener("change", function ()
{
    console.log(this.files[0])
    if (this.files[0])
    {
        let fr = new FileReader();

        fr.addEventListener("load", function ()
        {
            document.getElementsByClassName("image")[0].style.backgroundImage = "url(" + fr.result + ")";
        }, false);

        fr.readAsDataURL(this.files[0]);
    }
});
