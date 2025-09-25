// Fonction pour calculer la distance entre 2 coordonnées (Haversine)
function getDistance(lat1, lon1, lat2, lon2) {
  const R = 6371; // Rayon de la terre en km
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  const a = 
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
    Math.sin(dLon/2) * Math.sin(dLon/2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  return R * c; // distance en km
}

// Action quand on clique sur "Trouver un point de collecte"
document.querySelector('a[href="trouv.html"]').addEventListener("click", function(e){
  e.preventDefault(); // empêche la redirection
  if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(pos=>{
      const userLat = pos.coords.latitude;
      const userLon = pos.coords.longitude;

      // Calculer la distance de chaque point
      let proches = points.map(p=>{
        return {
          ...p,
          distance: getDistance(userLat, userLon, p.latitude, p.longitude)
        };
      });

      // Trier par distance croissante
      proches.sort((a,b)=>a.distance-b.distance);

      // Garder seulement les 5 plus proches (ou dans un rayon de 3 km par ex.)
      proches = proches.slice(0,5);

      // Nettoyer la carte (enlever anciens marqueurs sauf la tuile)
      map.eachLayer(layer=>{
        if(layer instanceof L.Marker || layer instanceof L.Circle) map.removeLayer(layer);
      });

      // Ajouter la position de l’utilisateur
      L.marker([userLat, userLon]).addTo(map).bindPopup("Vous êtes ici").openPopup();

      // Ajouter les points proches
      proches.forEach(p=>{
        const m = L.marker([p.latitude, p.longitude], {icon: binIcon}).addTo(map);
        m.bindPopup(`<b>${p.nom_pt}</b><br>${p.lieu}<br><i>${p.distance.toFixed(2)} km</i>`);
      });

      // Centrer la carte sur l’utilisateur
      map.setView([userLat, userLon], 14);

    }, ()=>{ alert("Impossible de récupérer votre position."); });
  } else {
    alert("La géolocalisation n'est pas supportée par votre navigateur.");
  }
});
