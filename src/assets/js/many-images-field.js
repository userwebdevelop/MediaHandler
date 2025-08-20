function sortable(parentElement, onUpdate) {
    let draggedElement;

    [...parentElement.children].forEach(item => item.draggable = true);

    parentElement.addEventListener('dragstart', event => {
        draggedElement = event.target.closest('.image-container');
        if (!draggedElement) return;

        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', '');
        draggedElement.classList.add('ghost');
    });

    parentElement.addEventListener('dragover', event => {
        event.preventDefault();
        const target = event.target.closest('.image-container');
        if (target && target !== draggedElement) {
            parentElement.insertBefore(draggedElement, event.clientX < target.getBoundingClientRect().left + target.offsetWidth / 2
                ? target : target.nextSibling);
        }
    });

    parentElement.addEventListener('dragend', () => {
        draggedElement.classList.remove('ghost');
        onUpdate();
    });
}

const parentElement = document.getElementById('sortable-images');
if (parentElement?.children.length) {
    sortable(parentElement, updateImageOrder);
}

function updateImageOrder() {
    const images = [...document.querySelectorAll('#sortable-images .image-container')];
    document.getElementById('image-order').value = JSON.stringify(images.map((img, index) => ({
        id: img.dataset.id,
        sort: index + 1
    })));
}

document.body.insertAdjacentHTML("beforeend", `
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmLabel">Подтверждение удаления</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Вы уверены, что хотите удалить это изображение?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                    <button id="confirmDelete" class="btn btn-danger">Удалить</button>
                </div>
            </div>
        </div>
    </div>
`);

let targetImage;
$("#confirmDelete").on("click", () => {
    if (targetImage) {
        deleteImage(targetImage);
    }
    $("#deleteConfirmModal").modal("hide");
});
function deleteImage(image) {
    const { name, field } = image.dataset;
    const input = document.getElementById('images-to-delete');
    const imagesToDelete = JSON.parse(input.value || '{}');
    imagesToDelete[field] = imagesToDelete[field] || [];

    if (imagesToDelete[field].includes(name)) {
        imagesToDelete[field] = imagesToDelete[field].filter(img => img !== name);
        if (!imagesToDelete[field].length) delete imagesToDelete[field];
    } else {
        imagesToDelete[field].push(name);
    }

    input.value = JSON.stringify(imagesToDelete);

    document.querySelectorAll(`.single-image[data-name="${name}"]`).forEach(img => {
        img.style.opacity = imagesToDelete[field]?.includes(name) ? "0.3" : "1";
        img.querySelector('.delete-image-btn').innerText = imagesToDelete[field]?.includes(name) ? 'Отмена' : '×';
    });
}
document.querySelectorAll('.single-image').forEach(image => {
    image.addEventListener('click', event => {
        let button = event.target.closest('.delete-image-btn');
        if (!button) return;
        event.preventDefault();

        const { name, field } = image.dataset;
        const input = document.getElementById('images-to-delete');
        const imagesToDelete = JSON.parse(input.value || '{}');

        if (imagesToDelete[field]?.includes(name)) {
            deleteImage(image);
        } else {
            targetImage = image;
            $("#deleteConfirmModal").modal("show");
        }
    });
});
