addFilter.onclick = function ()
{

    let inp = document.querySelector('.field').content;
    console.log(inp)
    let new_inp = inp.cloneNode(true)
    let div = document.getElementById('filters');

    div.appendChild(new_inp);
};
removeFilter.onclick = function ()
{
    let div = document.getElementById('filters');
    let last = div.querySelector('input:last-child');
    div.removeChild(last);
};

document.querySelector('.input').addEventListener("change", function ()
{
    console.log(this.files[0])
    if (this.files[0])
    {
        let fr = new FileReader();

        fr.addEventListener("load", function ()
        {
            document.getElementsByClassName("label")[0].style.backgroundImage = "url(" + fr.result + ")";
        }, false);

        fr.readAsDataURL(this.files[0]);
    }
});
