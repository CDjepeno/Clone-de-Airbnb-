"Use strict"
$('#add-image').click(function() {
    // récuperé le numéro des futurs champs qu'on va crée sous forme de nombre
    const INDEX =+$('#widgets-counter').val();
    
    // recuperé le prototype des entrées
    const TMPL = $('#ad_images').data('prototype').replace(/__name__/g, INDEX);

    // j'injecte ce code au sein de la div
    $('#ad_images').append(TMPL);

    $('#widgets-counter').val(INDEX + 1)

    // je gère le boutton supprimer
    handleDeleteButtons()
});

function handleDeleteButtons() {

    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        
        $(target).remove();
    })
}

function updateCounter() {
    const count = $('#ad_images div.form-group').length;

    $('#widgets-counter').val(count)
}
// appelle de la fonction au chargement de la page
updateCounter()
handleDeleteButtons()