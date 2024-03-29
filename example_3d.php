<?php require('views/_head.html'); ?>
<body class="text-center bg-dark text-light">
    <?php require('views/_navbar.php'); ?>
       
    <div class="container-fluid h-100 pt-5">
        <div style="position: relative;">
            <div style="position: absolute">
                <a style="decoration: none;"class="m-0 text-light h3" href="./learn.php"><i class="bi bi-arrow-left-circle"></i></a>
            </div>
            
        </div>
        <div class="row p-5 h-100">
            <div class="col">
                <div style="position: relative; top: 40%;">   
                    <div id="load_id" class="text-dark" style="position: absolute; left: 50%;transform: translateX(-50%)">
                        <div class="container">
                            <div class="row" id="row_load_id">
                                <div class="col-auto" >
                                    <span class="h2">Cargando Modelo</span>
                                    <br>
                                    <div id="color_load_id" >
                                        <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <br>
                                        <span id="span_load_id" class="h5">0</span><span class="h5">%</span>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>           
                </div>
                <div style="position: relative;">
                    <div style="position: absolute; left: 50%;transform: translateX(-50%)">
                        
                    </div>
                </div>
                
                <div id="id_scene" class="h-100"></div>
            </div>
        </div>
    </div>




<script type="module">

    import * as THREE from './libs/threejs/three.module.js';
    /*Is Neceesary Change modeule/
        import {
            ...,
            ...,
        } from './three.module.js';
    */
    import { OrbitControls } from './libs/threejs/OrbitControls.js';
    import { STLLoader } from './libs/threejs/STLLoader.js';

    var scene = new THREE.Scene();

    var renderer = new THREE.WebGLRenderer();
    
    var container=document.getElementById("id_scene");
    container.appendChild(renderer.domElement);
    renderer.setSize(container.clientWidth, container.clientHeight);    
    renderer.setClearColor(0xC0C0C0);

    //var camera = new THREE.OrthographicCamera(-1000, 10000, 10000, -10000, -10000, 10000);
    var camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
    

    var controls = new OrbitControls(camera, renderer.domElement);
    var loader = new STLLoader();
    var mesh,mesh_bool=false;
    loader.setCrossOrigin('anonymous');
    loader.load(
        "./example_3d/<?php echo rand(0, 2) ?>.stl", 
        function (geometry) 
        {
            try{
                document.getElementById("load_id").className="d-none";
                document.getElementById("id_accept").className="m-2 badge bg-primary";
                document.getElementById("id_download").className="m-2 badge bg-danger";
            }catch(err){}
            
            var material = new THREE.MeshPhongMaterial({color: 0xaaaaaa, specular: 0x111111, shininess: 200});
            mesh = new THREE.Mesh(geometry, material);
            mesh_bool=true;
            
            var box = new THREE.Box3().setFromObject(mesh);
            var center = box.getCenter(new THREE.Vector3());
            var size = box.getSize(new THREE.Vector3()).length();
            camera.lookAt(center);
            mesh.position.sub(center);
            
            camera.position.copy(center);
            camera.position.x = size /1.5 ;
            camera.position.y = size /1.5 ;
            camera.position.z = size /1.5  ;
            camera.lookAt(center);
            
            scene.add(mesh);
            
           
        },
        function (xhr) 
        {
            document.getElementById("span_load_id").innerHTML=((xhr.loaded / xhr.total) * 100).toFixed(2);
            if((xhr.loaded / xhr.total) > 0.75)
                document.getElementById("color_load_id").className="text-success";
            else if((xhr.loaded / xhr.total) > 0.50)
                document.getElementById("color_load_id").className="text-warning";
            else if((xhr.loaded / xhr.total )> 0.25)
                document.getElementById("color_load_id").className="text-dark";
            else
                document.getElementById("color_load_id").className="text-secondary";
        },
        function (error) 
        {
            
            document.getElementById("row_load_id").innerHTML="<div class='badge bg-danger'><span class='h4'>Error<br><span class='h6'>Error al cargar modelo</span></span></div>";
        }
        
    );
    
    // Luz arriba
    var directionalLightUp = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLightUp.position.set(0, 1, 0);
    scene.add(directionalLightUp);

    // Luz abajo
    var directionalLightDown = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLightDown.position.set(0, -1, 0);
    scene.add(directionalLightDown);

    // Luz derecha
    var directionalLightRight = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLightRight.position.set(1, 0, 0);
    scene.add(directionalLightRight);

    // Luz izquierda
    var directionalLightLeft = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLightLeft.position.set(-1, 0, 0);
    scene.add(directionalLightLeft);

    // Luz frente
    var directionalLightFront = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLightFront.position.set(0, 0, 1);
    scene.add(directionalLightFront);

    // Luz atrás
    var directionalLightBack = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLightBack.position.set(0, 0, -1);
    scene.add(directionalLightBack);

    // Luz indirecta superior derecha
    var directionalLightIndirectTopRight = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLightIndirectTopRight.position.set(1, 1, 0);
    scene.add(directionalLightIndirectTopRight);

    // Luz indirecta inferior derecha
    var directionalLightIndirectBottomRight = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLightIndirectBottomRight.position.set(1, -1, 0);
    scene.add(directionalLightIndirectBottomRight);

    // Luz indirecta inferior izquierda
    var directionalLightIndirectBottomLeft = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLightIndirectBottomLeft.position.set(-1, -1, 0);
    scene.add(directionalLightIndirectBottomLeft);


    

    
    // Función de animación
    function animate() 
    {
        requestAnimationFrame(animate);
        controls.update();
        //if(mesh_bool==true)
            //mesh.rotateOnAxis(new THREE.Vector3(0, 1, 0), 0.0005);
        
   
        renderer.render(scene, camera);
    }
    
    //Iniciar la animación
    animate();
    window.addEventListener(
        'resize',
        function() 
        {
            renderer.setSize(container.clientWidth, container.clientHeight);    
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
        }
    );

</script> 


</body>
<?php require 'views/_footer.html'; ?>
