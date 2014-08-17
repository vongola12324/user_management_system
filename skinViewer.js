/* skinViewer.js
 * @ref http://djazz.mine.nu/apps/MinecraftSkin/
 * @edited Davy
 * @lastedit 2012/04/07 03:40
 */
// shim layer with setTimeout fallback
window.requestAnimFrame = (function(){
	return window.requestAnimationFrame  || 
		window.webkitRequestAnimationFrame || 
		window.mozRequestAnimationFrame    || 
		window.oRequestAnimationFrame      || 
		window.msRequestAnimationFrame     || 
		function(/* function */ callback, /* DOMElement */ element){
			window.setTimeout(callback, 1000 / 60);
		};
})();

(function () {
/*	'use strict';*/
	var global = window;
	var supportWebGL = !!global.WebGLRenderingContext && (!!global.document.createElement('canvas').getContext('experimental-webgl') || !!global.document.createElement('canvas').getContext('webgl'));

	var getMaterial = function (img) {
		var material = new THREE.MeshBasicMaterial({
			map: new THREE.Texture(
				img,
				new THREE.UVMapping(),
				THREE.ClampToEdgeWrapping,
				THREE.ClampToEdgeWrapping,
				THREE.NearestFilter,
				THREE.LinearMipMapLinearFilter
			),
			transparent: true
		});
		material.map.needsUpdate = true;
		return material;
	};
	var uvmap = function (mesh, face, x, y, w, h, rotateBy) {
		if(!rotateBy) rotateBy = 0;
		var uvs = mesh.geometry.faceVertexUvs[0][face];
		var tileU = x;
		var tileV = y;
		
		uvs[ (0 + rotateBy) % 4 ].u = tileU * tileUvWidth;
		uvs[ (0 + rotateBy) % 4 ].v = tileV * tileUvHeight;
		uvs[ (1 + rotateBy) % 4 ].u = tileU * tileUvWidth;
		uvs[ (1 + rotateBy) % 4 ].v = tileV * tileUvHeight + h * tileUvHeight;
		uvs[ (2 + rotateBy) % 4 ].u = tileU * tileUvWidth + w * tileUvWidth;
		uvs[ (2 + rotateBy) % 4 ].v = tileV * tileUvHeight + h * tileUvHeight;
		uvs[ (3 + rotateBy) % 4 ].u = tileU * tileUvWidth + w * tileUvWidth;
		uvs[ (3 + rotateBy) % 4 ].v = tileV * tileUvHeight;
	};
	
	var cw = 320, ch = 480;
	var charImg = new Image();
	var cloakImg = new Image();
	var tileUvWidth = 1/64;
	var tileUvHeight = 1/32;
	
	var container = $('#skin #skinpreview')[0];
	var charcanvas = global.document.createElement('canvas');
	var cloakcanvas = global.document.createElement('canvas');
	var charc = charcanvas.getContext('2d');
	var cloakc = cloakcanvas.getContext('2d');
	charcanvas.width = cloakcanvas.width = 64;
	charcanvas.height = cloakcanvas.height = 32;
	var charMaterial = getMaterial(charcanvas); // empty image
	var cloakMaterial = getMaterial(cloakcanvas);
	var camera = new THREE.PerspectiveCamera(35, cw / ch, 1, 1000);
	camera.position.z = 50;
	//camera.target.position.y = -2;
	var scene = new THREE.Scene();
	scene.add(camera);
	
	var headgroup = new THREE.Object3D();
	var upperbody = new THREE.Object3D();
	var cloak3D = new THREE.Object3D();

	// Left leg
	var leftleggeo = new THREE.CubeGeometry(4, 12, 4);
	for(var i=0; i < 8; i+=1) {
		leftleggeo.vertices[i].position.y -= 6;
	}
	var leftleg = new THREE.Mesh(leftleggeo, charMaterial);
	leftleg.position.z = -2;
	leftleg.position.y = -6;
	uvmap(leftleg, 0, 8, 20, -4, 12);
	uvmap(leftleg, 1, 16, 20, -4, 12);
	uvmap(leftleg, 2, 4, 16, 4, 4, 3);
	uvmap(leftleg, 3, 8, 20, 4, -4, 1);
	uvmap(leftleg, 4, 12, 20, -4, 12);
	uvmap(leftleg, 5, 4, 20, -4, 12);
	scene.add(leftleg);
	
	
	// Right leg
	var rightleggeo = new THREE.CubeGeometry(4, 12, 4);
	for(var i=0; i < 8; i+=1) {
		rightleggeo.vertices[i].position.y -= 6;
	}
	var rightleg = new THREE.Mesh(rightleggeo, charMaterial);
	rightleg.position.z = 2;
	rightleg.position.y = -6;
	uvmap(rightleg, 0, 4, 20, 4, 12);
	uvmap(rightleg, 1, 12, 20, 4, 12);
	uvmap(rightleg, 2, 8, 16, -4, 4, 3);
	uvmap(rightleg, 3, 12, 20, -4, -4, 1);
	uvmap(rightleg, 4, 0, 20, 4, 12);
	uvmap(rightleg, 5, 8, 20, 4, 12);
	scene.add(rightleg);
	
	
	// Body
	var bodygeo = new THREE.CubeGeometry(4, 12, 8);
	var bodymesh = new THREE.Mesh(bodygeo, charMaterial);
	uvmap(bodymesh, 0, 20, 20, 8, 12);
	uvmap(bodymesh, 1, 32, 20, 8, 12);
	uvmap(bodymesh, 2, 20, 16, 8, 4, 1);
	uvmap(bodymesh, 3, 28, 16, 8, 4, 3);
	uvmap(bodymesh, 4, 16, 20, 4, 12);
	uvmap(bodymesh, 5, 28, 20, 4, 12);
	upperbody.add(bodymesh);
	
	
	// Left arm
	var leftarmgeo = new THREE.CubeGeometry(4, 12, 4);
	for(var i=0; i < 8; i+=1) {
		leftarmgeo.vertices[i].position.y -= 4;
	}
	var leftarm = new THREE.Mesh(leftarmgeo, charMaterial);
	leftarm.position.z = -6;
	leftarm.position.y = 4;
	leftarm.rotation.x = Math.PI/32;
	uvmap(leftarm, 0, 48, 20, -4, 12);
	uvmap(leftarm, 1, 56, 20, -4, 12);
	uvmap(leftarm, 2, 48, 16, -4, 4, 1);
	uvmap(leftarm, 3, 52, 16, -4, 4, 3);
	uvmap(leftarm, 4, 52, 20, -4, 12);
	uvmap(leftarm, 5, 44, 20, -4, 12);
	upperbody.add(leftarm);
	
	// Right arm
	var rightarmgeo = new THREE.CubeGeometry(4, 12, 4);
	for(var i=0; i < 8; i+=1) {
		rightarmgeo.vertices[i].position.y -= 4;
	}
	var rightarm = new THREE.Mesh(rightarmgeo, charMaterial);
	rightarm.position.z = 6;
	rightarm.position.y = 4;
	rightarm.rotation.x = -Math.PI/32;
	uvmap(rightarm, 0, 44, 20, 4, 12);
	uvmap(rightarm, 1, 52, 20, 4, 12);
	uvmap(rightarm, 2, 44, 16, 4, 4, 1);
	uvmap(rightarm, 3, 48, 16, 4, 4, 3);
	uvmap(rightarm, 4, 40, 20, 4, 12);
	uvmap(rightarm, 5, 48, 20, 4, 12);
	upperbody.add(rightarm);
	
	// Head
	var headgeo = new THREE.CubeGeometry(8, 8, 8);
	var headmesh = new THREE.Mesh(headgeo, charMaterial);
	headmesh.position.y = 2;
	uvmap(headmesh, 0, 8, 8, 8, 8);
	uvmap(headmesh, 1, 24, 8, 8, 8);
	
	uvmap(headmesh, 2, 8, 0, 8, 8, 1);
	uvmap(headmesh, 3, 16, 0, 8, 8, 3);
	
	uvmap(headmesh, 4, 0, 8, 8, 8);
	uvmap(headmesh, 5, 16, 8, 8, 8);
	headgroup.add(headmesh);
	
	// Mask
	var helmetgeo = new THREE.CubeGeometry(8.5, 8.5, 8.5);
	var helmetmesh = new THREE.Mesh(helmetgeo, charMaterial);
	helmetmesh.doubleSided = true;
	helmetmesh.position.y = 2;
	uvmap(helmetmesh, 0, 32+8, 8, 8, 8);
	uvmap(helmetmesh, 1, 32+24, 8, 8, 8);
	
	uvmap(helmetmesh, 2, 32+8, 0, 8, 8, 1);
	uvmap(helmetmesh, 3, 32+16, 0, 8, 8, 3);
	
	uvmap(helmetmesh, 4, 32+0, 8, 8, 8);
	uvmap(helmetmesh, 5, 32+16, 8, 8, 8);
	headgroup.add(helmetmesh);
	
	// Cloak
	var cloakgeo = new THREE.CubeGeometry(1, 16, 10);
	var cloakmesh = new THREE.Mesh(cloakgeo, cloakMaterial);
	cloakmesh.position.x = 0;
	cloakmesh.position.y = -10;
	cloakmesh.rotation.y = Math.PI;
	uvmap(cloakmesh, 0, 1, 1, 10, 16);
	uvmap(cloakmesh, 1, 12, 1, 10, 16);

	uvmap(cloakmesh, 2, 1, 0, 10, 1);
	uvmap(cloakmesh, 3, 11, 0, 10, 1);

	uvmap(cloakmesh, 4, 0, 1, 1, 16);
	uvmap(cloakmesh, 5, 11, 1, 1, 16);
	cloak3D.add(cloakmesh);

	headgroup.position.y = 8;
	cloak3D.position.x = -2;
	cloak3D.position.y = 8;
	scene.add(upperbody);
	scene.add(headgroup);
	scene.add(cloak3D);	

	var mouseX = 0;
	var mouseY = 0.1;
	var originMouseX = 0;
	var originMouseY = 0;
	
	var rad = 0;
	
	var isMouseOver = false;
	var isMouseDown = false;
	
	var counter = 0;
	var firstRender = true;
	var render = function () {
		requestAnimFrame(render, renderer.domElement);
		var oldRad = rad;
		if(!isMouseDown) {
			//mouseX*=0.95;
			mouseY*=0.97;
			rad += 2;
		}
		else {
			rad = mouseX;
		}
		camera.position.x = -Math.cos(rad/(cw/2)+(Math.PI/0.9));
		camera.position.z = -Math.sin(rad/(cw/2)+(Math.PI/0.9));
		camera.position.y = (mouseY/(ch/2))*1.5+0.2;
		camera.position.setLength(60);
		camera.lookAt(new THREE.Vector3(0, -2, 0));
		counter+=0.01;
		
		headgroup.rotation.y = Math.sin(counter*3)/5;
		headgroup.rotation.z = Math.sin(counter*2)/6;
		
		leftarm.rotation.z = -Math.sin(counter*3)/2;
		leftarm.rotation.x = (Math.cos(counter*3)+Math.PI/2)/30;
		rightarm.rotation.z = Math.sin(counter*3)/2;
		rightarm.rotation.x = -(Math.cos(counter*3)+Math.PI/2)/30;
		
		leftleg.rotation.z = Math.sin(counter*4)/3;
		rightleg.rotation.z = -Math.sin(counter*4)/3;
		
		cloak3D.rotation.z = -0.01-(Math.cos(counter*3)+Math.PI/2)/15;

		renderer.render(scene, camera);
	};
	if(supportWebGL) {
		var renderer = new THREE.WebGLRenderer({antialias: true});
	}
	else {
		var renderer = new THREE.CanvasRenderer({antialias: true});
	}
	var threecanvas = renderer.domElement;
	renderer.sortObjects = false;
	renderer.setSize(cw, ch);
	//renderer.setClearColorHex(0x000000, 0.25);
	container.appendChild(threecanvas);
	
	var onMouseMove = function (e) {
		if(isMouseDown) {
			mouseX = (e.pageX - threecanvas.offsetLeft - originMouseX);
			mouseY = (e.pageY - threecanvas.offsetTop - originMouseY);
		}
	};
	
	threecanvas.addEventListener('mousedown', function (e) {
		e.preventDefault();
		originMouseX = (e.pageX - threecanvas.offsetLeft) - rad;
		originMouseY = (e.pageY - threecanvas.offsetTop) - mouseY;
		isMouseDown = true;
		isMouseOver = true;
		onMouseMove(e);
	}, false);
	global.addEventListener('mouseup', function (e) {
		isMouseDown = false;
	}, false);
	global.addEventListener('mousemove', onMouseMove, false);
	threecanvas.addEventListener('mouseout', function (e) {
		isMouseOver = false;
	}, false);
	
	render();
	
	charImg.onload = function () {
		charc.clearRect(0, 0, 64, 32);
		charc.drawImage(charImg, 0, 0);
		
		// Code to replace black with transparent black
		/*var imagedata = charc.getImageData(0, 0, 64, 32);
		var imgd = imagedata.data;
		for(var i=0; i < imgd.length; i+=4) {
			if(imgd[0] === 0 && imgd[1] === 0 && imgd[2] === 0 && imgd[3] === 255) {
				imgd[3] = 0;
			}
		}
		charc.putImageData(imagedata, 0, 0);*/
		
		charMaterial.map.needsUpdate = true;
	};

	cloakImg.onload = function () {
		cloakc.clearRect(0, 0, 64, 32);
		cloakc.drawImage(cloakImg, 0, 0);

		cloakMaterial.map.needsUpdate = true;
	};

	charImg.src = $skinPath;
	cloakImg.src = $skinCloakPath;
	container.appendChild(charImg);
	container.appendChild(cloakImg);
/*
// Davy: Wait for advence edit. Orz	
	threecanvas.addEventListener('dragenter', function (e) {
		e.stopPropagation(); 
		e.preventDefault();
		threecanvas.className = "dragenter";
	}, false);
	threecanvas.addEventListener('dragleave', function (e) {
		e.stopPropagation(); 
		e.preventDefault();
		threecanvas.className = "";
	}, false);
	threecanvas.addEventListener('dragover', function (e) {
		e.stopPropagation(); 
		e.preventDefault();
	}, false);
	threecanvas.addEventListener('drop', function (e) {
		e.stopPropagation();
		e.preventDefault();
		threecanvas.className = "";
		
		var dt = e.dataTransfer;
		var files = dt.files;
		handleFiles(files);
	}, false);
*/
})();
