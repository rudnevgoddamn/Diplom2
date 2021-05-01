

    addFilter.onclick = function ()
{

    let inp = document.querySelector('.field').content;
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


