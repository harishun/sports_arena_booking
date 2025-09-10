function toggle_overlay(overlay){
   // Toggle the visibility of the overlay itself
   overlay.classList.toggle('hidden');
   // Toggle the scrolling lock on the main page content
   document.body.classList.toggle('overlay-active');
}

function previewImage(input,previewContainer) {
  const file = input.files[0];
  if (!file) return;

  const url = URL.createObjectURL(file);
  
  // Set background image
  previewContainer.style.backgroundImage = `url(${url})`;
  previewContainer.style.backgroundSize = 'cover';
  previewContainer.style.backgroundPosition = 'center';
  previewContainer.style.backgroundRepeat = 'no-repeat';
  previewContainer.style.borderRadius = '15px';
  
  // Optionally remove the SVG icon once image is set
  previewContainer.innerHTML = '';

  // Free memory after image loads
  const img = new Image();
  img.src = url;
  img.onload = () => URL.revokeObjectURL(url);
}