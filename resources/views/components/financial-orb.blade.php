<div id="orb-container" class="relative w-full h-96 cursor-grab"></div>
<div id="chart-tooltip" class="absolute bg-gray-700 text-white text-xs p-2 rounded shadow-lg pointer-events-none opacity-0 transition-opacity duration-200 z-50"></div>
<div id="orb-legend" class="mt-4 p-4 bg-gray-800/50 backdrop-blur-xl border border-white/10 rounded-lg text-white">
    <h4 class="text-lg font-semibold mb-2">Monthly Financial Summary</h4>
    <ul class="space-y-1">
    </ul>
</div>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script> --}}

<script>
    const container = document.getElementById('orb-container');
    const legendContainer = document.getElementById('orb-legend').querySelector('ul');
    const tooltip = document.getElementById('chart-tooltip');

    if (typeof THREE === 'undefined') {
        console.error('THREE.js is not loaded. Please ensure the script tag is correct in your main layout.');
        if (container) {
            container.innerHTML = `<div class="flex items-center justify-center h-full text-red-400">3D visualization not available: THREE.js not loaded.</div>`;
        }
        throw new Error("THREE.js is not loaded.");
    }

    if (container) {
        // --- Scene Setup ---
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setPixelRatio(window.devicePixelRatio);
        container.appendChild(renderer.domElement);

        camera.position.set(0, 5, 12); // Posisikan kamera agar melihat grafik dari depan atas
        camera.lookAt(0, 0, 0); // Arahkan kamera ke titik pusat

        // --- Lighting ---
        const ambientLight = new THREE.AmbientLight(0xffffff, 1.0);
        scene.add(ambientLight);
        const directionalLight1 = new THREE.DirectionalLight(0xffffff, 1.5); // Perbaikan penulisan DirectionionalLight menjadi DirectionalLight
        directionalLight1.position.set(5, 10, 5).normalize();
        scene.add(directionalLight1);

        const directionalLight2 = new THREE.DirectionalLight(0xffffff, 0.8);
        directionalLight2.position.set(-5, 5, -5).normalize(); // Cahaya dari arah lain
        scene.add(directionalLight2);
        
        const pointLight = new THREE.PointLight(0xffffff, 0.5, 100); // Cahaya titik
        pointLight.position.set(0, 10, 0);
        scene.add(pointLight);

        // Raycaster for interaction
        const raycaster = new THREE.Raycaster();
        const mouse = new THREE.Vector2();
        let intersectedObject = null;
        let originalColor = {}; 
        let chartGroup; // Group to hold all bars and lines

        let interactableObjects = []; // Array to store all meshes that can be interacted with

        const dataUrl = '{{ route("dashboard.monthly-summary-chart") }}'; // Ubah URL data

        // Helper function to format currency in JavaScript
        function formatCurrencyJS(amount, currencyCode = 'USD', locale = 'id-ID') {
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: currencyCode,
                minimumFractionDigits: 0, 
                maximumFractionDigits: 2,
            }).format(amount);
        }

        // --- Data Fetching & Chart Creation ---
        fetch(dataUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.length === 0) {
                    container.innerHTML = `<div class="flex items-center justify-center h-full text-gray-400">No monthly summary data available.</div>`;
                    return;
                }
                createCombinedChart(data);
                animate();
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
                container.innerHTML = `<div class="flex items-center justify-center h-full text-red-400">Could not load chart data: ${error.message}. Please check console for details.</div>`;
            });

        function createCombinedChart(data) {
            chartGroup = new THREE.Group();
            scene.add(chartGroup); 
            interactableObjects = []; // Reset interactable objects

            const allAmounts = data.flatMap(item => [item.income, item.expense, item.net_balance]);
            const maxVal = Math.max(...allAmounts);
            const minVal = Math.min(...allAmounts);
            const range = maxVal - minVal;

            const yScale = 8; // Max height/scale for bars and line points
            const yOffset = -4; // Base position for the chart on the Y-axis

            // Normalize and scale function
            const getScaledY = (value) => {
                if (range === 0) return yScale / 2; // Avoid division by zero, place at mid-height
                return ((value - minVal) / range) * yScale;
            };

            const barWidth = 0.8;
            const barDepth = 0.6;
            const groupSpacing = 2.5; // Spacing between monthly groups
            const barOffset = barWidth / 2 + 0.1; // Offset for income/expense bars within a month

            const linePoints = []; // To store points for the net balance line
            const pointRadius = 0.1; // Radius for line points

            data.forEach((item, index) => {
                const xPos = index * groupSpacing - (data.length - 1) * groupSpacing / 2; // Center the chart

                // --- Income Bar ---
                const incomeHeight = getScaledY(item.income) || 0.1; // Min height 0.1 for visibility
                const incomeGeometry = new THREE.BoxGeometry(barWidth, incomeHeight, barDepth);
                const incomeMaterial = new THREE.MeshStandardMaterial({
                    color: 0x4CAF50, // Green for Income
                    metalness: 0.7,
                    roughness: 0.3,
                    transparent: true,
                    opacity: 0.9,
                });
                const incomeBar = new THREE.Mesh(incomeGeometry, incomeMaterial);
                incomeBar.position.set(xPos - barOffset, yOffset + incomeHeight / 2, 0); // Offset to the left
                incomeBar.userData = {
                    type: 'Income',
                    month: item.month,
                    amount: item.income,
                    originalPosition: incomeBar.position.clone(),
                    isHovered: false
                };
                chartGroup.add(incomeBar);
                interactableObjects.push(incomeBar);
                originalColor[incomeBar.uuid] = incomeMaterial.color.getHex();

                // --- Expense Bar ---
                const expenseHeight = getScaledY(item.expense) || 0.1; // Min height 0.1 for visibility
                const expenseGeometry = new THREE.BoxGeometry(barWidth, expenseHeight, barDepth);
                const expenseMaterial = new THREE.MeshStandardMaterial({
                    color: 0xF44336, // Red for Expense
                    metalness: 0.7,
                    roughness: 0.3,
                    transparent: true,
                    opacity: 0.9,
                });
                const expenseBar = new THREE.Mesh(expenseGeometry, expenseMaterial);
                expenseBar.position.set(xPos + barOffset, yOffset + expenseHeight / 2, 0); // Offset to the right
                expenseBar.userData = {
                    type: 'Expense',
                    month: item.month,
                    amount: item.expense,
                    originalPosition: expenseBar.position.clone(),
                    isHovered: false
                };
                chartGroup.add(expenseBar);
                interactableObjects.push(expenseBar);
                originalColor[expenseBar.uuid] = expenseMaterial.color.getHex();

                // --- Line Chart Points (Net Balance) ---
                const netBalanceY = yOffset + getScaledY(item.net_balance); // Scaled Y position for net balance
                const netBalancePoint = new THREE.Vector3(xPos, netBalanceY, barDepth / 2 + 0.1); // Slightly in front of bars
                linePoints.push(netBalancePoint);

                // Add small spheres for each point on the line
                const pointGeometry = new THREE.SphereGeometry(pointRadius, 16, 16);
                const pointMaterial = new THREE.MeshBasicMaterial({ color: 0x2196F3 }); // Blue for Net Balance
                const pointMesh = new THREE.Mesh(pointGeometry, pointMaterial);
                pointMesh.position.copy(netBalancePoint);
                pointMesh.userData = {
                    type: 'Net Balance',
                    month: item.month,
                    amount: item.net_balance,
                    originalPosition: pointMesh.position.clone(),
                    isHovered: false
                };
                chartGroup.add(pointMesh);
                interactableObjects.push(pointMesh);
                originalColor[pointMesh.uuid] = pointMaterial.color.getHex();

                // Add to legend (clear existing first)
                if (index === 0) {
                    legendContainer.innerHTML = ''; // Clear existing legend items
                    const incomeLegendItem = document.createElement('li');
                    incomeLegendItem.className = 'flex items-center space-x-2';
                    incomeLegendItem.innerHTML = `
                        <span style="background-color: #4CAF50;" class="block w-4 h-4 rounded-full"></span>
                        <span>Income</span>
                    `;
                    legendContainer.appendChild(incomeLegendItem);

                    const expenseLegendItem = document.createElement('li');
                    expenseLegendItem.className = 'flex items-center space-x-2';
                    expenseLegendItem.innerHTML = `
                        <span style="background-color: #F44336;" class="block w-4 h-4 rounded-full"></span>
                        <span>Expense</span>
                    `;
                    legendContainer.appendChild(expenseLegendItem);

                    const netBalanceLegendItem = document.createElement('li'); // Add net balance to legend
                    netBalanceLegendItem.className = 'flex items-center space-x-2';
                    netBalanceLegendItem.innerHTML = `
                        <span style="background-color: #2196F3;" class="block w-4 h-4 rounded-full"></span>
                        <span>Net Balance</span>
                    `;
                    legendContainer.appendChild(netBalanceLegendItem);
                }

                // Add Month Label to Legend below Income/Expense for each month
                const monthSummaryItem = document.createElement('li');
                monthSummaryItem.className = 'flex items-center space-x-2 text-gray-400 mt-2 border-t border-white/10 pt-2'; // Added border-t
                monthSummaryItem.innerHTML = `
                    <span><strong>${item.month}</strong>: 
                    Income ${formatCurrencyJS(item.income, 'IDR', 'id-ID')}, 
                    Expense ${formatCurrencyJS(item.expense, 'IDR', 'id-ID')}, 
                    Net ${formatCurrencyJS(item.net_balance, 'IDR', 'id-ID')}</span>`;
                legendContainer.appendChild(monthSummaryItem);

            });

            // --- Draw Net Balance Line ---
            if (linePoints.length > 1) {
                const lineMaterial = new THREE.LineBasicMaterial({ color: 0x2196F3, linewidth: 3 }); // Blue line
                const lineGeometry = new THREE.BufferGeometry().setFromPoints(linePoints);
                const line = new THREE.Line(lineGeometry, lineMaterial);
                chartGroup.add(line);
            }

            // --- Base Plane (Grid) ---
            const planeWidth = data.length * groupSpacing + barWidth * 2;
            const planeHeight = yScale + 2; // Adjusted to cover max height of bars
            
            const basePlaneGeometry = new THREE.PlaneGeometry(planeWidth, planeHeight, data.length, 1); 
            const basePlaneMaterial = new THREE.MeshBasicMaterial({
                color: 0xAAAAAA, 
                transparent: true,
                opacity: 0.1, 
                side: THREE.DoubleSide
            });
            const basePlane = new THREE.Mesh(basePlaneGeometry, basePlaneMaterial);
            basePlane.rotation.x = -Math.PI / 2; 
            basePlane.position.y = yOffset + yScale / 2; // Position in the middle of the scaled Y range
            basePlane.position.z = -barDepth / 2 - 0.5; 
            chartGroup.add(basePlane);

            // Add a lighter grid for the reference
            const gridHelper = new THREE.GridHelper(planeWidth, data.length, 0x555555, 0x555555);
            gridHelper.position.y = yOffset; // Align with the actual base of the chart
            gridHelper.position.z = -barDepth / 2 - 0.5;
            chartGroup.add(gridHelper);

            // Center the chart group
            // chartGroup.position.y = yOffset; // The yOffset is already applied to bars/points, no need to shift group
        }

        // --- Animation & Interaction ---
        let isDragging = false;
        let previousMousePosition = { x: 0, y: 0 };
        
        function animate() {
            requestAnimationFrame(animate);

            if (interactableObjects.length > 0) { 
                raycaster.setFromCamera(mouse, camera);
                const intersects = raycaster.intersectObjects(interactableObjects); 

                if (intersects.length > 0) {
                    if (intersectedObject != intersects[0].object) {
                        // Restore previous object's state
                        if (intersectedObject) {
                            if (intersectedObject.material.type === "MeshStandardMaterial" || intersectedObject.material.type === "MeshBasicMaterial") {
                                intersectedObject.material.color.setHex(originalColor[intersectedObject.uuid]);
                            }
                            intersectedObject.position.copy(intersectedObject.userData.originalPosition);
                            intersectedObject.userData.isHovered = false;
                            tooltip.classList.add('opacity-0'); // Hide tooltip
                        }

                        intersectedObject = intersects[0].object;
                        // Store original color and position
                        if (!intersectedObject.userData.isHovered) { 
                            intersectedObject.userData.originalPosition = intersectedObject.position.clone();
                        }
                        if (intersectedObject.material.type === "MeshStandardMaterial" || intersectedObject.material.type === "MeshBasicMaterial") {
                            originalColor[intersectedObject.uuid] = intersectedObject.material.color.getHex();
                        }

                        // Apply hover effect: brighter color and slight extrusion
                        if (intersectedObject.material.type === "MeshStandardMaterial" || intersectedObject.material.type === "MeshBasicMaterial") {
                            intersectedObject.material.color.setHex(originalColor[intersectedObject.uuid] | 0x333333); // Make slightly brighter
                        }
                        
                        const extrusionDepth = intersectedObject.userData.type === 'Net Balance' ? 0.1 : 0.2; 
                        const extrusionVector = new THREE.Vector3(0, 0, extrusionDepth); 
                        if (intersectedObject.userData.type !== 'Net Balance' && intersectedObject.userData.type !== 'Income' && intersectedObject.userData.type !== 'Expense') { 
                            // Only apply rotation to bars, not points on line
                            extrusionVector.applyQuaternion(intersectedObject.quaternion); 
                        }
                        intersectedObject.position.add(extrusionVector);
                        intersectedObject.userData.isHovered = true;

                        // Show tooltip
                        tooltip.innerHTML = `<strong>${intersectedObject.userData.type} - ${intersectedObject.userData.month}</strong><br>${formatCurrencyJS(intersectedObject.userData.amount, 'IDR', 'id-ID')}`;
                        tooltip.classList.remove('opacity-0');
                    }
                } else {
                    if (intersectedObject) {
                        // Restore original state
                        if (intersectedObject.material.type === "MeshStandardMaterial" || intersectedObject.material.type === "MeshBasicMaterial") {
                            intersectedObject.material.color.setHex(originalColor[intersectedObject.uuid]);
                        }
                        intersectedObject.position.copy(intersectedObject.userData.originalPosition);
                        intersectedObject.userData.isHovered = false;
                        tooltip.classList.add('opacity-0'); // Hide tooltip
                    }
                    intersectedObject = null;
                }
            }
            renderer.render(scene, camera);
        }

        // --- Mouse Controls ---
        container.addEventListener('mousedown', (e) => { 
            isDragging = true; 
            previousMousePosition = { x: e.offsetX, y: e.offsetY };
        });
        window.addEventListener('mouseup', () => { isDragging = false; });
        container.addEventListener('mousemove', (e) => {
            // Update mouse coordinates for raycasting
            const rect = container.getBoundingClientRect();
            mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = - ((e.clientY - rect.top) / rect.height) * 2 + 1;

            // Update tooltip position
            tooltip.style.left = `${e.clientX + 10}px`;
            tooltip.style.top = `${e.clientY + 10}px`;

            if (!isDragging || !chartGroup) return;
            const deltaMove = {
                x: e.offsetX - previousMousePosition.x,
                y: e.offsetY - previousMousePosition.y
            };

            chartGroup.rotation.y += deltaMove.x * 0.005; 
            chartGroup.rotation.x += deltaMove.y * 0.005;
            // Limit rotation to avoid flipping
            chartGroup.rotation.x = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, chartGroup.rotation.x));

            previousMousePosition = { x: e.offsetX, y: e.offsetY };
        });

        // --- Responsive Canvas ---
        window.addEventListener('resize', () => {
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        });
    } else {
        // Fallback for when container or THREE.js is not available
        if (container) {
            container.innerHTML = `<div class="flex items-center justify-center h-full text-red-400">3D visualization not available.</div>`;
        }
    }
</script>
