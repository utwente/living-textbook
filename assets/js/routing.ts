// @ts-ignore
import Routing from '@fos/router.min.js';
// @ts-ignore
import routes from '@/_fos_routes.json';

Routing.setRoutingData(routes);
(window as any).Routing = Routing;

export interface RoutingInterface {
  generate(route: string, params?: { [key: string]: string }, absolute?: boolean): string;
}

export default Routing as RoutingInterface;
