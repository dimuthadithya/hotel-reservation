// Bootstrap alert helper function
function showBootstrapAlert(type, title, message) {
  let icon = '';
  switch (type) {
    case 'success':
      icon = 'check-circle';
      break;
    case 'error':
      icon = 'exclamation-circle';
      type = 'danger';
      break;
    case 'warning':
      icon = 'exclamation-triangle';
      break;
    case 'info':
      icon = 'info-circle';
      break;
  }

  return `
    <div class='alert alert-${type} alert-dismissible fade show' role='alert'>
        <strong><i class='fas fa-${icon} me-2'></i>${title}</strong>
        ${message ? `<br>${message}` : ''}
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>`;
}

document.addEventListener('DOMContentLoaded', function () {
  const addAmenityForm = document.getElementById('addAmenityForm');
  const editAmenityForm = document.getElementById('editAmenityForm');
  const addAmenityModal = new bootstrap.Modal(
    document.getElementById('addAmenityModal')
  );
  const editAmenityModal = new bootstrap.Modal(
    document.getElementById('editAmenityModal')
  );

  // Add Amenity Form Handler
  if (addAmenityForm) {
    addAmenityForm.addEventListener('submit', async function (e) {
      e.preventDefault();

      try {
        const formData = new FormData(addAmenityForm);

        const response = await fetch('handlers/add_amenity.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          // Add the new amenity to the appropriate category list
          const categoryList = document.getElementById(
            data.amenity.category + 'Amenities'
          );
          if (categoryList) {
            // Remove "No amenities found" message if it exists
            const noAmenitiesMsg = categoryList.querySelector(
              '.list-group-item:only-child'
            );
            if (noAmenitiesMsg && noAmenitiesMsg.textContent.includes('No')) {
              noAmenitiesMsg.remove();
            }

            // Create new amenity element
            const amenityElement = document.createElement('div');
            amenityElement.className =
              'list-group-item d-flex justify-content-between align-items-center';
            amenityElement.innerHTML = `
                            <div>
                                <i class="fas fa-${
                                  data.amenity.icon_class
                                } me-2"></i>
                                ${data.amenity.amenity_name}
                                ${
                                  data.amenity.description
                                    ? `<small class="text-muted d-block">${data.amenity.description}</small>`
                                    : ''
                                }
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary edit-amenity" 
                                        data-id="${data.amenity.amenity_id}"
                                        data-name="${data.amenity.amenity_name}"
                                        data-icon="${data.amenity.icon_class}"
                                        data-category="${data.amenity.category}"
                                        data-description="${
                                          data.amenity.description || ''
                                        }">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-amenity" 
                                        data-id="${data.amenity.amenity_id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;

            categoryList.appendChild(amenityElement);
          } // Show success message
          const alertArea = document.querySelector('.amenities-list');
          alertArea.insertAdjacentHTML(
            'afterbegin',
            showBootstrapAlert(
              'success',
              'Success',
              'Amenity added successfully!'
            )
          );

          // Reset form and close modal
          addAmenityForm.reset();
          addAmenityModal.hide();
        } else {
          throw new Error(data.message);
        }
      } catch (error) {
        alert('Error: ' + error.message);
      }
    });
  }

  // Edit Amenity Form Handler
  if (editAmenityForm) {
    editAmenityForm.addEventListener('submit', async function (e) {
      e.preventDefault();

      try {
        const formData = new FormData(editAmenityForm);

        const response = await fetch('handlers/edit_amenity.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          // Find and update the amenity element
          const amenityElement = document
            .querySelector(`[data-id="${data.amenity.amenity_id}"]`)
            .closest('.list-group-item');
          const oldCategory = amenityElement
            .closest('.list-group')
            .id.replace('Amenities', '');

          // If category changed, move the element to the new category list
          if (oldCategory !== data.amenity.category) {
            const newCategoryList = document.getElementById(
              data.amenity.category + 'Amenities'
            );

            // Remove from old category
            amenityElement.remove();

            // Add to new category
            if (newCategoryList) {
              // Remove "No amenities found" message if it exists
              const noAmenitiesMsg = newCategoryList.querySelector(
                '.list-group-item:only-child'
              );
              if (noAmenitiesMsg && noAmenitiesMsg.textContent.includes('No')) {
                noAmenitiesMsg.remove();
              }

              newCategoryList.appendChild(amenityElement);
            }

            // Check if old category is now empty
            const oldCategoryList = document.getElementById(
              oldCategory + 'Amenities'
            );
            if (oldCategoryList && oldCategoryList.children.length === 0) {
              oldCategoryList.innerHTML = `<div class="list-group-item text-muted">No ${oldCategory} amenities found</div>`;
            }
          }

          // Update the amenity content
          amenityElement.innerHTML = `
                        <div>
                            <i class="fas fa-${
                              data.amenity.icon_class
                            } me-2"></i>
                            ${data.amenity.amenity_name}
                            ${
                              data.amenity.description
                                ? `<small class="text-muted d-block">${data.amenity.description}</small>`
                                : ''
                            }
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary edit-amenity" 
                                    data-id="${data.amenity.amenity_id}"
                                    data-name="${data.amenity.amenity_name}"
                                    data-icon="${data.amenity.icon_class}"
                                    data-category="${data.amenity.category}"
                                    data-description="${
                                      data.amenity.description || ''
                                    }">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-amenity" 
                                    data-id="${data.amenity.amenity_id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `; // Show success message
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Amenity updated successfully!'
          });

          // Close modal
          editAmenityModal.hide();
        } else {
          throw new Error(data.message);
        }
      } catch (error) {
        alert('Error: ' + error.message);
      }
    });
  }

  // Edit button click handler
  document.addEventListener('click', function (e) {
    if (e.target.closest('.edit-amenity')) {
      const button = e.target.closest('.edit-amenity');
      const amenityId = button.dataset.id;
      const amenityName = button.dataset.name;
      const iconClass = button.dataset.icon;
      const category = button.dataset.category;
      const description = button.dataset.description;

      // Populate the edit form
      document.getElementById('editAmenityId').value = amenityId;
      document.getElementById('editAmenityName').value = amenityName;
      document.getElementById('editIconClass').value = iconClass;
      document.getElementById('editCategory').value = category;
      document.getElementById('editDescription').value = description;

      // Show the modal
      editAmenityModal.show();
    }
  });

  // Delete button click handler
  document.addEventListener('click', function (e) {
    if (e.target.closest('.delete-amenity')) {
      const button = e.target.closest('.delete-amenity');
      const amenityId = button.dataset.id;
      Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this amenity?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          deleteAmenity(amenityId);
        }
      });
    }
  });

  // Delete amenity function
  async function deleteAmenity(amenityId) {
    try {
      const formData = new FormData();
      formData.append('amenityId', amenityId);

      const response = await fetch('handlers/delete_amenity.php', {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        // Remove the amenity element
        const amenityElement = document
          .querySelector(`[data-id="${amenityId}"]`)
          .closest('.list-group-item');
        const categoryList = amenityElement.closest('.list-group');

        amenityElement.remove();

        // If category is now empty, show "No amenities" message
        if (categoryList.children.length === 0) {
          const categoryName = categoryList.id.replace('Amenities', '');
          categoryList.innerHTML = `<div class="list-group-item text-muted">No ${categoryName} amenities found</div>`;
        }
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: 'Amenity deleted successfully!'
        });
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.error('Error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: error.message || 'Failed to delete amenity. Please try again.'
      });
    }
  }
});
